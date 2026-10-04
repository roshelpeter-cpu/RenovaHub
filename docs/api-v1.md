# RenovaHub API v1

Internal API for the homeowner workspace. It does not call Google Places, a payment provider, or Brevo.

Authentication: `Authorization: Bearer {sanctum-token}`  
Base path: `/api/v1`  
Rate limit: 60 requests per minute per user.

Every JSON response uses:

```json
{ "success": true, "message": "...", "data": {} }
```

Validation errors return HTTP 422:

```json
{ "success": false, "message": "Validation failed.", "errors": {} }
```

Missing authentication is HTTP 401. Another homeowner's project is HTTP 403. An unknown id is HTTP 404.

List endpoints include `meta.current_page`, `meta.last_page`, `meta.per_page`, and `meta.total`.

| Method | Endpoint | Purpose |
| --- | --- | --- |
| GET | `/api/v1/projects` | List the authenticated homeowner's projects |
| POST | `/api/v1/projects` | Create a project. `user_id` in the body is ignored |
| GET | `/api/v1/projects/{project}` | Show one owned project |
| PUT | `/api/v1/projects/{project}` | Update the brief. Progress and quotation totals are not accepted |
| DELETE | `/api/v1/projects/{project}` | Delete an owned project |
| GET | `/api/v1/projects/{project}/tasks` | List tasks |
| GET | `/api/v1/projects/{project}/documents` | List documents. File bytes stay behind the web download route |
| GET | `/api/v1/projects/{project}/quotations` | List quotations |
| POST | `/api/v1/projects/{project}/quotations/{quotation}/approve` | Approve a quotation on that project |
| POST | `/api/v1/projects/{project}/quotations/{quotation}/reject` | Reject a quotation on that project |
| GET | `/api/v1/projects/{project}/change-requests` | List change requests |
| POST | `/api/v1/projects/{project}/change-requests` | Submit a change request |
| GET | `/api/v1/projects/{project}/messages` | List project messages |
| POST | `/api/v1/projects/{project}/messages` | Send a message. Body: `{ "body": "..." }` |
| GET | `/api/v1/projects/{project}/payments` | List payment records. No card data is returned |
| GET | `/api/v1/projects/{project}/notifications` | List the authenticated user's notifications |

Create project body:

```json
{
  "name": "Garden Room",
  "description": "A shaded room opening to the garden.",
  "renovation_type": "outdoor",
  "property_type": "house"
}
```

Passwords, OAuth secrets, and payment credentials are never included in `data`.
