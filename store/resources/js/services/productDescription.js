const escapeHtml = value => value.replace(/&/g, '&amp;').replace(/</g, '&lt;')
  .replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;')

const paragraph = text => `<p>${escapeHtml(text)}</p>`

const formatParagraph = text => {
  // Supplier descriptions often embed a long compatibility list in one sentence.
  const compatibility = text.match(/^(.*?\b(?:compatible with|compatibility:|for use with|is for)\s+)([\s\S]+)$/i)
  if (compatibility) {
    const warranty = compatibility[2].match(/\s+((?:Lifetime\s+)?Limited Warranty\b[\s\S]*)$/i)
    const models = (warranty ? compatibility[2].slice(0, warranty.index) : compatibility[2])
      .split(/,\s+/).map(model => model.trim()).filter(Boolean)
    if (models.length >= 3) {
      return paragraph(compatibility[1].trim())
        + `<ul class="product-compatibility-list">${models.map(model => `<li>${escapeHtml(model)}</li>`).join('')}</ul>`
        + (warranty ? paragraph(warranty[1]) : '')
    }
  }
  if (text.length > 500) {
    const sentences = text.split(/(?<=[.!?])\s+(?=[A-Z])/)
    const paragraphs = []
    let current = ''
    for (const sentence of sentences) {
      if (current && current.length + sentence.length > 400) {
        paragraphs.push(paragraph(current))
        current = ''
      }
      current += `${current ? ' ' : ''}${sentence}`
    }
    if (current) paragraphs.push(paragraph(current))
    return paragraphs.join('')
  }
  return paragraph(text)
}

export const formatPlainDescription = value => {
  const lines = String(value || '').replace(/\r\n?/g, '\n').split('\n')
  const output = []
  let text = []
  let items = []
  let listType = ''
  const flushText = () => {
    if (text.length) output.push(formatParagraph(text.join(' ')))
    text = []
  }
  const flushList = () => {
    if (items.length) output.push(`<${listType}>${items.map(item => `<li>${escapeHtml(item)}</li>`).join('')}</${listType}>`)
    items = []
    listType = ''
  }
  for (const raw of lines) {
    const line = raw.trim()
    if (!line) {
      flushText()
      flushList()
      continue
    }
    const bullet = line.match(/^(?:[-*\u2022]\s+|\d+[.)]\s+)(.+)$/)
    if (bullet) {
      flushText()
      const type = /^\d/.test(line) ? 'ol' : 'ul'
      if (listType && listType !== type) flushList()
      listType = type
      items.push(bullet[1])
      continue
    }
    flushList()
    if (/^#{1,4}\s+/.test(line) || (line.length < 80 && line.endsWith(':'))) {
      flushText()
      output.push(`<h4>${escapeHtml(line.replace(/^#{1,4}\s+/, '').replace(/:$/, ''))}</h4>`)
    } else {
      text.push(line)
    }
  }
  flushText()
  flushList()
  return output.join('')
}

export const formatProductDescription = value => {
  const text = String(value || '').trim()
  if (!text) return ''
  if (!/<\/?[a-z][^>]*>|&(?:#\d+|#x[\da-f]+|[a-z]+);/i.test(text)) return formatPlainDescription(text)

  // Reconstruct inert formatting only; never forward source attributes or URLs.
  const document = new DOMParser().parseFromString(text, 'text/html')
  const blocked = new Set(['SCRIPT', 'STYLE', 'IFRAME', 'OBJECT', 'EMBED', 'SVG', 'MATH', 'TEMPLATE', 'NOSCRIPT', 'FORM', 'INPUT', 'BUTTON'])
  const inline = new Set(['STRONG', 'EM', 'B', 'I', 'BR'])
  const render = (node, inParagraph = false) => {
    if (node.nodeType === 3) {
      if (!node.textContent.trim()) return inParagraph ? ' ' : ''
      return inParagraph ? escapeHtml(node.textContent) : formatPlainDescription(node.textContent)
    }
    if (node.nodeType !== 1) return ''
    const name = node.tagName.toUpperCase()
    if (blocked.has(name)) return ''
    const tag = name.toLowerCase()
    const children = (context = inParagraph) => [...node.childNodes].map(child => render(child, context)).join('')
    if (inline.has(name)) return tag === 'br' ? '<br>' : `<${tag}>${children(true)}</${tag}>`
    if (name === 'P') {
      if ([...node.children].every(child => child.tagName === 'BR')) {
        return formatPlainDescription([...node.childNodes].map(child => child.nodeName === 'BR' ? '\n' : child.textContent).join(''))
      }
      return node.children.length === 0 ? formatPlainDescription(node.textContent) : `<p>${children(true)}</p>`
    }
    if (/^H[1-6]$/.test(name)) return `<h4>${children(true)}</h4>`
    if (name === 'UL' || name === 'OL') {
      return `<${tag}>${[...node.children].filter(child => child.tagName === 'LI').map(child => render(child, true)).join('')}</${tag}>`
    }
    if (name === 'LI') return `<li>${children(true)}</li>`
    if (name === 'TD' || name === 'TH') return `${children(true)} `
    if (name === 'TR') return `<p>${children(true)}</p>`
    return children()
  }
  return [...document.body.childNodes].map(node => render(node)).join('')
}
