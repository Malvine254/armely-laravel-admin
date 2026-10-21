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
const { default: ProductCard } = await import(`data:text/javascript;base64,${Buffer.from(script).toString('base64')}`)
const product = {
  productId: '15428226',
  productName: 'MANUFACTURER RENEWED HP ZBOOK 8 14 INCH',
  mfgPartNo: 'BX8M7UTR#ABA',
  vendorName: 'TD SYNNEX',
  description: 'Renewed supplier laptop',
}
const render = props => renderToString(createSSRApp(ProductCard, { product, ...props }))

test('outside-catalog matches use the shared card without unverified purchase or stock claims', async () => {
  const html = await render({ discovery: true })
  assert.match(html, /product-card-shell/)
  assert.match(html, /Supplier match/)
  assert.match(html, /TD SYNNEX SKU: 15428226/)
  assert.match(html, /Configuration, pricing and availability require verification/)
  assert.match(html, /View online details &amp; image/)
  assert.doesNotMatch(html, /Add to Quote|Add to Favorites|Share Product|Available now|No reviews|>Active</)
})

test('discovery cards accept enriched online images and disable details during lookups', async () => {
  const html = await render({ discovery: true, image: 'https://images.example.com/zbook.jpg', busy: true })
  assert.match(html, /https:\/\/images\.example\.com\/zbook\.jpg/)
  assert.match(html, /disabled/)
  assert.match(html, /Looking up online details/)
})

test('regular catalog cards keep their existing actions and availability presentation', async () => {
  const html = await render({ product: { ...product, availableQuantity: 3 } })
  assert.match(html, /Add to Quote/)
  assert.match(html, /Add to Favorites/)
  assert.match(html, /Share Product/)
  assert.match(html, /Available now/)
  assert.doesNotMatch(html, /Supplier match|View online details &amp; image/)
})
