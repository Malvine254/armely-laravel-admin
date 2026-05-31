# Resources API

Base URL:

`https://armely.com`

## Authentication

This API now uses the existing admin login.

Requirements:

1. Log in at `/admin/login`
2. Use the same browser session or session cookie when calling `/api/resources`
3. For `POST` requests, include the CSRF token from the logged-in admin session

If you are not logged in as an active admin, the API returns `401 Unauthorized`.

Direct credential option:

1. Send `X-Admin-Email`
2. Send `X-Admin-Password`
3. The API will authenticate that admin for the request without a prior browser login

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

Browser example after admin login:

```js
fetch('/api/resources', {
  credentials: 'include',
  headers: {
    'Accept': 'application/json'
  }
}).then(r => r.json()).then(console.log)
```

Direct credential example:

```js
fetch('/api/resources', {
  headers: {
    'Accept': 'application/json',
    'X-Admin-Email': 'admin.armely@armely.com',
    'X-Admin-Password': '@armelyLLC$'
  }
}).then(r => r.json()).then(console.log)
```

## Get one resource

Use the slug returned from `/api/resources`.

Example slug:

`microsoft-fabric-for-energy`

Request:

```http
GET /api/resources/microsoft-fabric-for-energy
Accept: application/json
```

Example:

```bash
curl https://armely.com/api/resources/microsoft-fabric-for-energy
```

## Get permanent access links for gated resources

Use this for PDF resources where `requires_customer_access` is `true`.

Request:

```http
POST /api/resources/microsoft-fabric-for-energy/access-links
Content-Type: application/json
Accept: application/json
```

Sample JSON body:

Minimum required fields:

```json
{
  "name": "Jane Doe",
  "email": "jane@example.com"
}
```

Optional full example:

```json
{
  "name": "Jane Doe",
  "email": "jane@example.com",
  "organization": "Armely",
  "job_title": "Operations Manager",
  "message": "Please send the resource"
}
```

Example:

```bash
curl -X POST https://armely.com/api/resources/microsoft-fabric-for-energy/access-links \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Jane Doe",
    "email": "jane@example.com",
    "organization": "Armely",
    "job_title": "Operations Manager",
    "message": "Please send the resource"
  }'
```

Browser example after admin login:

```js
fetch('/api/resources/microsoft-fabric-for-energy/access-links', {
  method: 'POST',
  credentials: 'include',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
  },
  body: JSON.stringify({
    name: 'Jane Doe',
    email: 'jane@example.com'
  })
}).then(r => r.json()).then(console.log)
```

Direct credential example:

```js
fetch('/api/resources/microsoft-fabric-for-energy/access-links', {
  method: 'POST',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'X-Admin-Email': 'admin.armely@armely.com',
    'X-Admin-Password': '@armelyLLC$'
  },
  body: JSON.stringify({
    name: 'Jane Doe',
    email: 'jane@example.com'
  })
}).then(r => r.json()).then(console.log)
```

## Response shape

`GET /api/resources` returns:

```json
{
  "success": true,
  "resources": [],
  "featured_resources": [],
  "categories": [],
  "types": [],
  "stats": {
    "total_resources": 0,
    "total_categories": 0,
    "last_updated_at": null
  },
  "filters": {
    "search": "",
    "category": "",
    "type": "",
    "sort": "newest"
  }
}
```

`POST /access-links` returns:

```json
{
  "success": true,
  "resource": {
    "id": 1,
    "title": "Microsoft Fabric for Energy",
    "slug": "microsoft-fabric-for-energy",
    "category": "Microsoft Fabric",
    "type": "pdf",
    "resource_url": "https://armely.com/resources/...",
    "download_url": "https://armely.com/resources/.../download?...",
    "requires_customer_access": true
  },
  "customer": {
    "name": "Jane Doe",
    "email": "jane@example.com",
    "organization": "Armely",
    "job_title": "Operations Manager"
  },
  "links": {
    "resource_url": "https://armely.com/resources/...",
    "download_url": "https://armely.com/resources/.../download?..."
  }
}
```