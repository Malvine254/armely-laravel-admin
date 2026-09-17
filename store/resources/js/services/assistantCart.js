// This is called only for a new response or an explicit product-card click.
export const applyAssistantCartOperation = async (store, operation, loadProduct) => {
  if (!['add_to_cart', 'prepare_quote', 'set_cart_quantity', 'remove_from_cart'].includes(operation?.type) || operation.items?.length !== 1) return false
  const item = operation.items[0]

  if (operation.type === 'remove_from_cart') {
    const target = store.items.find(row => String(row.productId) === String(item.productId))
    if (!target) return false
    return store.removeItem(target.productId) !== false
  }

  const quantity = Number(item.quantity)
  if (!Number.isInteger(quantity) || quantity < 1 || quantity > 10000) return false
  const product = await loadProduct(item.productId)
  if (!product?.productId || product.discontinueProduct || product.isAvailable === false) return false
  const stock = Number(product.availableQuantity ?? product.totalQuantity ?? product.qty ?? NaN)
  if (Number.isFinite(stock) && stock <= 0 && !product.supplierOrderable) return false
  const existing = store.items.find(row => String(row.productId) === String(product.productId))
  // A quantity change replaces the line total; only add_to_cart stacks on top of what is there.
  if (operation.type !== 'add_to_cart' && existing) {
    return store.updateQuantity(existing.productId, quantity)
  }
  return store.addItem({ ...product, productId: existing?.productId ?? product.productId }, quantity)
}
