We need to add a hidden public Resources feature to the existing Armely website.

Goal:
Create a clean, branded resources system where admins can upload PDFs, one-pagers, images, and short videos, then share public links in emails or campaigns. The resources should be publicly accessible by URL, but should NOT appear in the main website navigation/menu.

Requirements:

1. Public Resources Page
Add a new public route:

/resources

This page should match the existing Armely website theme, layout, colors, spacing, typography, buttons, and responsiveness.

The page should look professional and enterprise-ready, not plain or disconnected from the rest of the site.

It should include:
- Hero section with title: Resources
- Short subtitle: Practical guides, checklists, and insights from Armely
- Resource cards/grid
- Category/filter support if easy
- Search bar if easy
- Each resource card should show:
  - Title
  - Short description
  - Type: PDF, Video, Checklist, Guide, etc.
  - Date added
  - CTA button: View Resource or Download

Do not add Resources to the main menu unless explicitly enabled later.

2. Public Resource Detail Page
Add route:

/resources/[slug]

Each resource should have a clean landing page with:
- Title
- Description
- Resource type
- Preview or thumbnail if available
- Download/View button
- Optional embedded video player if the resource is a video
- Back to resources link

Example public URL:

https://armely.com/resources/field-data-to-copilot-checklist

3. Admin Resource Management
Create an admin-only area for managing resources.

Suggested route:

/admin/resources

Admin should be able to:
- View all uploaded resources
- Add a new resource
- Edit existing resource
- Delete resource
- Copy public link

Resource fields:
- Title
- Slug, auto-generated from title but editable
- Description
- Category
- Resource type: PDF, Video, Image, Guide, Checklist
- File upload
- Thumbnail upload, optional
- Published status: draft/published
- Featured status, optional
- Created date / updated date

Only published resources should appear publicly.

4. File Storage
Use the best existing storage pattern in the current website.

Preferred:
- Azure Blob Storage for PDFs, images, and videos

If Azure Blob Storage is not already configured, add a clean storage abstraction so we can swap between local storage and Azure Blob later.

Environment variables should be used for storage credentials.

Example:
AZURE_STORAGE_CONNECTION_STRING=
AZURE_STORAGE_CONTAINER_NAME=resources

Files should be uploaded to a resources container/folder.

5. Database
Add a Resource model/table using the current database setup.

Suggested fields:
- id
- title
- slug
- description
- category
- resourceType
- fileUrl
- fileName
- thumbnailUrl
- isPublished
- isFeatured
- createdAt
- updatedAt

Slug must be unique.

6. Security
Admin upload/manage pages must be protected using the website’s existing authentication/admin access pattern.

Public resource pages do not require login.

Do not expose storage credentials to the frontend.

Validate file uploads:
- Allow PDF, image, and video formats only
- Add max file size limits
- Use safe filenames
- Prevent duplicate slug conflicts

7. SEO and Visibility
Resources should be publicly accessible by direct URL.

Do not add the Resources link to the main navigation.

Add noindex support if needed, especially for resources that should not show in Google.

For now:
- Public route exists
- Not linked in menu
- Direct URL works

8. UI/UX
The resources page should feel like part of Armely’s website.

Follow existing:
- Header/footer structure
- Button styles
- Card styles
- Colors
- Typography
- Spacing
- Mobile responsiveness
- Dark/light styling if the site supports it

The design should look modern, polished, and consulting/enterprise focused.

9. Sharing Flow
On the admin resource list, add a “Copy Link” button.

When clicked, it should copy:

https://armely.com/resources/[slug]

This link will be used in emails like:

Download: Field Data to Copilot Checklist →

10. Acceptance Criteria
The feature is complete when:
- /resources loads and matches the site theme
- /resources/[slug] opens a resource landing page
- Resources are not shown in the main nav
- Admin can upload/create/edit/delete resources
- Admin can copy share links
- Only published resources appear publicly
- Uploaded files can be viewed or downloaded
- Mobile view works well
- No credentials are exposed client-side
- Code follows the existing project structure and style

Before coding:
1. Inspect the current project structure.
2. Identify framework, routing, auth, database, and styling approach.
3. Reuse existing components wherever possible.
4. Keep implementation consistent with the current website.
5. Do not rewrite unrelated parts of the site.