import test from 'node:test'
import assert from 'node:assert/strict'
import { readFile } from 'node:fs/promises'
import { formatPlainDescription } from '../../resources/js/services/productDescription.js'

test('formats overview, headings, feature lists and warranty without changing facts', () => {
  assert.equal(formatPlainDescription('Memory upgrade.\n\nFeatures:\n- 16GB DDR4\n- 3200MHz\n\nLifetime Limited Warranty'),
    '<p>Memory upgrade.</p><h4>Features</h4><ul><li>16GB DDR4</li><li>3200MHz</li></ul><p>Lifetime Limited Warranty</p>')
})

test('formats already-saved supplier compatibility text as individual list entries', () => {
  const description = 'Total Micro 16GB DDR4 module is for the HP EliteBook 630 G9, 630 G10, ProBook 440 G9, ZBook 15 G6. Lifetime Limited Warranty'
  assert.equal(formatPlainDescription(description),
    '<p>Total Micro 16GB DDR4 module is for</p><ul class="product-compatibility-list"><li>the HP EliteBook 630 G9</li><li>630 G10</li><li>ProBook 440 G9</li><li>ZBook 15 G6.</li></ul><p>Lifetime Limited Warranty</p>')
})

test('does not turn an ordinary prose comma or numeric capacity into a compatibility list', () => {
  const text = 'For everyday work, this module has 16GB capacity, DDR4 memory and a 1,200MHz clock.'
  assert.equal(formatPlainDescription(text), `<p>${text}</p>`)
})

test('supports numbered lists, Windows newlines and wrapped paragraphs', () => {
  assert.equal(formatPlainDescription('Overview\r\ncontinued.\r\n\r\n1. First item\r\n2. Second item'),
    '<p>Overview continued.</p><ol><li>First item</li><li>Second item</li></ol>')
})

test('escapes source text rather than treating it as trusted markup', () => {
  assert.equal(formatPlainDescription('- <img src=x onerror=alert(1)>\n- A & B'),
    '<ul><li>&lt;img src=x onerror=alert(1)&gt;</li><li>A &amp; B</li></ul>')
  assert.equal(formatPlainDescription(''), '')
})

test('breaks long prose at sentence boundaries, retaining all text', () => {
  const text = ['An existing product description has a sentence with supplied information. '.repeat(10).trim()].join('')
  const html = formatPlainDescription(text)
  assert.ok((html.match(/<p>/g) || []).length > 1)
  assert.equal(html.replace(/<\/p><p>/g, ' ').replace(/<\/?p>/g, ''), text)
})

test('the product view uses the shared formatter and explicit list styles', async () => {
  const [view, css] = await Promise.all([
    readFile(new URL('../../resources/js/views/catalog/ProductDetail.vue', import.meta.url), 'utf8'),
    readFile(new URL('../../resources/css/app.css', import.meta.url), 'utf8'),
  ])
  assert.match(view, /return formatProductDescription\(desc\)/)
  assert.match(view, /class="product-description /)
  assert.match(view, /v-html="productDescription"/)
  assert.match(view, /v-html="specificationDescription"/)
  assert.match(css, /\.product-description ul\s*\{\s*list-style: disc;/)
  assert.match(css, /\.product-description ol\s*\{\s*list-style: decimal;/)
})
