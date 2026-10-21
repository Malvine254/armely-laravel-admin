import test from 'node:test'
import assert from 'node:assert/strict'
import { readFile } from 'node:fs/promises'
import { parse, compileScript } from '@vue/compiler-sfc'
import { createSSRApp } from 'vue'
import { renderToString } from '@vue/server-renderer'

const source = await readFile(new URL('../../resources/js/components/ProductCard.vue', import.meta.url), 'utf8')
const { descriptor } = parse(source)
const compiled = compileScript(descriptor, { id: 'product-card-test', inlineTemplate: true })
const script = compiled.content
  .replace(/from ['"]vue['"]/g, `from '${import.meta.resolve('vue')}'`)
  .replace("import { usePricingSettings } from '../composables/usePricingSettings'", () => "const usePricingSettings = () => ({ loadPricingSettings: async () => {}, convertFromUsd: value => value, formatWithCurrency: value => '$' + value })")
  .replace("import { isSupplierOrderable } from '../services/productAvailability'", 'const isSupplierOrderable = () => false')
  .replace('const pricingReady = ref(false)', 'const pricingReady = ref(true)')
const { default: ProductCard } = await import(`data:text/javascript;base64,${Buffer.from(script).toString('base64')}`)
const product = {
  productId: '15428226',
  productName: 'MANUFACTURER RENEWED HP ZBOOK 8 14 INCH',
  mfgPartNo: 'BX8M7UTR#ABA',
  vendorName: 'TD SYNNEX',
  description: 'Renewed supplier laptop',
}
const render = props => renderToString(createSSRApp(ProductCard, { product, ...props }))

test('persisted outside-catalog matches use all normal product actions', async () => {
  const html = await render({ product: { ...product, availableQuantity: 4, productPrice: [{ rsPrice: 100, msrp: 150 }] } })
  assert.match(html, /product-card-shell/)
  assert.match(html, /SKU: BX8M7UTR#ABA/)
  assert.match(html, />View</)
  assert.match(html, /Add to Quote/)
  assert.match(html, /Add to Favorites/)
  assert.match(html, /Share Product/)
  assert.match(html, /No reviews/)
  assert.doesNotMatch(html, /View online details/)
})

test('discovery uses catalog price formatting, MSRP savings and real supplier quantities', async () => {
  const html = await render({
    product: { ...product, productPrice: [{ rsPrice: 90, msrp: 150, minQty: 1 }], availableQuantity: 4 },
  })
  assert.match(html, /\$90/)
  assert.match(html, /MSRP: \$150/)
  assert.match(html, /Save 40%/)
  assert.match(html, /Stock: 4/)
  assert.doesNotMatch(html, /Pricing unavailable|pricing and availability require verification/)
})

test('zero stock discovery results show out of stock rather than available now', async () => {
  const html = await render({ product: { ...product, availableQuantity: 0 } })
  assert.match(html, /Out of stock/)
  assert.doesNotMatch(html, /Available now|Supplier orderable/)
})

test('automatic enrichment does not replace or disable the normal View action', async () => {
  const html = await render({ image: 'https://images.example.com/zbook.jpg' })
  assert.match(html, /https:\/\/images\.example\.com\/zbook\.jpg/)
  assert.match(html, />View</)
  assert.doesNotMatch(html, /Loading|View online details/)
})

test('regular catalog cards keep their existing actions and availability presentation', async () => {
  const html = await render({ product: { ...product, availableQuantity: 3 } })
  assert.match(html, /Add to Quote/)
  assert.match(html, /Add to Favorites/)
  assert.match(html, /Share Product/)
  assert.match(html, /Available now/)
  assert.doesNotMatch(html, /Outside catalog|View online details &amp; image/)
})
