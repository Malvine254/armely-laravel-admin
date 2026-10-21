export const waitForDiscoveryContent = async (request, isCurrent, delay = (ms) => new Promise((resolve) => setTimeout(resolve, ms))) => {
  for (let attempt = 0; attempt < 6; attempt++) {
    if (!isCurrent()) return null
    const response = await request()
    if (!isCurrent()) return null
    if (!response.content_pending) return response
    if (attempt < 5) await delay(10000)
  }
  throw new Error('Product images are being processed in the background. Please check again shortly.')
}
