# Resources API

Base URL: `https://armely.com`

## Authentication

No authentication is required for these endpoints.

## Hosting behavior

Resource files are hosted as permanent static assets under `/resources/*`.

- No authentication
- No signed URLs
- No expiry

## List all resources

Request:

```http
GET /api/resources
Accept: application/json
```

Example:

```bash
curl https://armely.com/api/resources
```

## Get one resource

Use a slug from `/api/resources`.

Request:

```http
GET /api/resources/microsoft-fabric-for-energy
Accept: application/json
```

Example:

```bash
curl https://armely.com/api/resources/microsoft-fabric-for-energy
```

## Get link payload for a resource

This endpoint is now a convenience endpoint that returns permanent links.

Request:

```http
POST /api/resources/microsoft-fabric-for-energy/access-links
Accept: application/json
```

Example:

```bash
curl -X POST https://armely.com/api/resources/microsoft-fabric-for-energy/access-links
```

## Response fields for bots

Each resource payload now includes:

- `asset_url`: permanent static file URL (for example: `https://armely.com/resources/files/...pdf`)
- `download_url`: permanent URL to the file (same permanent behavior)
- `resource_url`: resource details page URL
- `requires_customer_access`: always `false`

Example resource payload:

```json
{
  "id": 1,
  "title": "Microsoft Fabric for Energy",
  "slug": "microsoft-fabric-for-energy",
  "category": "Microsoft Fabric",
  "type": "pdf",
  "asset_url": "https://armely.com/resources/files/20260602123456-resource.pdf",
  "resource_url": "https://armely.com/resources/microsoft-fabric-for-energy",
  "download_url": "https://armely.com/resources/files/20260602123456-resource.pdf",
  "requires_customer_access": false
}
```