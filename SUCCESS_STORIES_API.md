# Success Stories API - FlutterFlow Integration Guide

## Overview

The Success Stories API allows you to fetch all completed returns (success stories) from your FindIt application. This is a **public endpoint** that doesn't require authentication.

---

## API Endpoints

### 1. Get All Success Stories (PUBLIC)

**Endpoint:**
```
GET /api/success-stories
```

**Method:** GET  
**Authentication:** Not required  
**Description:** Retrieve paginated list of all completed returns (success stories)

**Response Example:**
```json
{
  "data": [
    {
      "id": 1,
      "match_id": 5,
      "return_date": "2025-12-08",
      "notes": "Item was successfully returned to the owner in perfect condition.",
      "proof_image": "https://your-domain.com/storage/return-proofs/abc123.jpg",
      "status": "completed",
      "created_at": "2025-12-08T10:30:00Z",
      "updated_at": "2025-12-08T10:30:00Z",
      "match": {
        "id": 5,
        "lost_item": {
          "id": 3,
          "title": "iPhone 14",
          "description": "Silver iPhone 14 with a crack on the right side",
          "category": "electronics",
          "location": "Mall parking lot",
          "date_lost": "2025-12-05",
          "image_url": "https://your-domain.com/storage/items/item1.jpg",
          "status": "found",
          "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "phone_number": "+1234567890"
          }
        },
        "found_item": {
          "id": 2,
          "title": "iPhone found at mall",
          "description": "Found a silver iPhone 14 with a crack",
          "category": "electronics",
          "location": "Mall parking lot",
          "date_found": "2025-12-05",
          "image_url": "https://your-domain.com/storage/items/item2.jpg",
          "status": "claimed",
          "user": {
            "id": 2,
            "name": "Jane Smith",
            "email": "jane@example.com",
            "phone_number": "+0987654321"
          }
        }
      }
    }
  ],
  "links": {
    "first": "http://your-domain.com/api/success-stories?page=1",
    "last": "http://your-domain.com/api/success-stories?page=5",
    "prev": null,
    "next": "http://your-domain.com/api/success-stories?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "path": "http://your-domain.com/api/success-stories",
    "per_page": 20,
    "to": 20,
    "total": 100
  }
}
```

**Query Parameters:**
- `page` (optional, integer) - Page number for pagination (default: 1)
- `per_page` (optional, integer) - Items per page (default: 20, max: 100)

**Example Request:**
```bash
curl "https://your-domain.com/api/success-stories?page=1"
```

**HTTP Status Codes:**
- `200 OK` - Successfully retrieved success stories
- `401 Unauthorized` - Invalid or missing token (if authentication is added)
- `500 Internal Server Error` - Server error

---

### 2. Get User's Returns (PROTECTED - Requires Authentication)

**Endpoint:**
```
GET /api/returns/my
```

**Method:** GET  
**Authentication:** Required (Bearer token)  
**Description:** Retrieve returns created by the authenticated user

**Headers:**
```
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json
```

**Response Format:** Same as success stories endpoint but filtered to user's returns

**Example Request:**
```bash
curl -X GET "https://your-domain.com/api/returns/my" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Content-Type: application/json"
```

---

### 3. Create a Return Record (PROTECTED - Requires Authentication)

**Endpoint:**
```
POST /api/returns
```

**Method:** POST  
**Authentication:** Required (Bearer token)  
**Description:** Create a new return record when items are successfully returned

**Headers:**
```
Authorization: Bearer YOUR_API_TOKEN
Content-Type: multipart/form-data
```

**Request Body:**
```json
{
  "match_id": 5,
  "notes": "Item was returned in perfect condition",
  "return_date": "2025-12-08",
  "proof_image": <FILE>  // Optional file upload
}
```

**Fields:**
- `match_id` (required, integer) - The match ID linking lost and found items
- `notes` (optional, string, max 1000 chars) - Additional notes about the return
- `return_date` (required, date) - Date of return in YYYY-MM-DD format
- `proof_image` (optional, file) - Image proof of return (max 5MB, image only)

**Example Request with cURL:**
```bash
curl -X POST "https://your-domain.com/api/returns" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -F "match_id=5" \
  -F "notes=Item returned successfully" \
  -F "return_date=2025-12-08" \
  -F "proof_image=@/path/to/image.jpg"
```

**Response (201 Created):**
```json
{
  "data": {
    "id": 1,
    "match_id": 5,
    "return_date": "2025-12-08",
    "notes": "Item returned successfully",
    "proof_image": "https://your-domain.com/storage/return-proofs/abc123.jpg",
    "status": "completed",
    "created_at": "2025-12-08T10:30:00Z"
  }
}
```

---

### 4. Get Specific Return Record (PUBLIC)

**Endpoint:**
```
GET /api/returns/{id}
```

**Method:** GET  
**Authentication:** Not required  
**Description:** Get details of a specific return record

**Example Request:**
```bash
curl "https://your-domain.com/api/returns/1"
```

**Response (200 OK):**
```json
{
  "data": {
    "id": 1,
    "match_id": 5,
    "return_date": "2025-12-08",
    "notes": "Item returned successfully",
    "proof_image": "https://your-domain.com/storage/return-proofs/abc123.jpg",
    "status": "completed",
    "created_at": "2025-12-08T10:30:00Z"
  }
}
```

---

## FlutterFlow Integration Steps

### Step 1: Add Success Stories List View

In FlutterFlow:

1. **Add API Call**
   - API Name: `GetSuccessStories`
   - Method: GET
   - URL: `https://your-domain.com/api/success-stories`
   - No authentication needed

2. **Parse Response**
   - Path to data: `data`
   - Item count path: `meta.total`
   - Has pagination: ✅ Yes
   - Next page path: `links.next`

3. **Display in ListView**
   - Create a ListView widget
   - Set "Items from API": `GetSuccessStories`
   - Map fields:
     - Title: `match.lostItem.title`
     - Description: `match.lostItem.description`
     - Image: `match.lostItem.imageUrl`
     - Lost by: `match.lostItem.user.name`
     - Found by: `match.foundItem.user.name`
     - Return date: `returnDate`
     - Notes: `notes`

### Step 2: Add Success Story Detail View

1. **Create Detail Page**
   - Parameter: `returnRecord` (type: JSON)

2. **Display Details**
   - Title, description, images
   - Names of both users
   - Contact information
   - Return date and notes
   - Proof image (if available)

### Step 3: Add Create Return Form (After Matching)

1. **Add Form Fields**
   - Match ID (hidden/auto-filled)
   - Notes (text field)
   - Return date (date picker)
   - Proof image (image picker)

2. **Add Submit Button**
   - Method: POST
   - URL: `https://your-domain.com/api/returns`
   - Headers: `Authorization: Bearer ${FFAppState.authToken}`
   - Body: Form data
   - On success: Show confirmation and refresh success stories list

---

## Authentication for Protected Endpoints

For endpoints requiring authentication, include the Bearer token from login:

**Headers:**
```
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json
```

Get your token from the login endpoint:
```bash
POST /api/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password123"
}
```

Response includes:
```json
{
  "token": "YOUR_API_TOKEN",
  "user": {...}
}
```

Store this token in FlutterFlow state and use it in subsequent requests.

---

## Common Response Fields

### Return Record Object
```json
{
  "id": 1,
  "match_id": 5,
  "return_date": "2025-12-08",
  "notes": "Additional notes",
  "proof_image": "https://url-to-image.jpg",
  "status": "completed",
  "created_at": "2025-12-08T10:30:00Z",
  "updated_at": "2025-12-08T10:30:00Z",
  "match": {...}
}
```

### Match Object (Nested)
```json
{
  "id": 5,
  "lost_item": {...},
  "found_item": {...}
}
```

### Item Object (Lost/Found)
```json
{
  "id": 1,
  "title": "Item Title",
  "description": "Item Description",
  "category": "electronics",
  "location": "Location",
  "image_url": "https://url-to-image.jpg",
  "status": "found",
  "user": {...}
}
```

### User Object
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "phone_number": "+1234567890"
}
```

---

## Error Responses

### 400 Bad Request
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "match_id": ["The match id field is required."]
  }
}
```

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
  "message": "Unauthorized"
}
```

### 404 Not Found
```json
{
  "message": "Return record not found"
}
```

### 422 Unprocessable Entity
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["Error message"]
  }
}
```

---

## Example FlutterFlow Configuration

### API Call Setup
```
Name: GetSuccessStories
Method: GET
URL: https://your-domain.com/api/success-stories
Authentication: None
Headers:
  - Content-Type: application/json

Response Model:
{
  "data": [
    {
      "id": Number,
      "match": {
        "lostItem": {
          "title": String,
          "description": String,
          "imageUrl": String,
          "user": {
            "name": String,
            "email": String
          }
        },
        "foundItem": {
          "user": {
            "name": String,
            "email": String
          }
        }
      },
      "returnDate": String,
      "notes": String,
      "proofImage": String
    }
  ],
  "meta": {
    "currentPage": Number,
    "lastPage": Number,
    "total": Number
  }
}
```

---

## Testing

### Using cURL

**Get Success Stories:**
```bash
curl -X GET "http://localhost:8000/api/success-stories" \
  -H "Content-Type: application/json"
```

**Create Return Record:**
```bash
curl -X POST "http://localhost:8000/api/returns" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "match_id=5" \
  -F "notes=Success" \
  -F "return_date=2025-12-08" \
  -F "proof_image=@image.jpg"
```

### Using Postman

1. Create new request
2. Method: GET or POST
3. URL: `http://your-domain.com/api/success-stories`
4. Headers: Add `Content-Type: application/json`
5. For protected endpoints: Add `Authorization: Bearer YOUR_TOKEN`
6. Send request

---

## Troubleshooting

**502 Bad Gateway Error:**
- Ensure Laravel is running: `php artisan serve`
- Check PHP error logs
- Verify database connection
- Check if all migrations are completed

**401 Unauthorized:**
- Token may have expired
- Use the login endpoint to get a fresh token
- Ensure token is passed correctly in Authorization header

**404 Not Found:**
- Verify the endpoint URL
- Check if return record exists
- Ensure correct match ID is used

**422 Validation Error:**
- Verify all required fields are provided
- Check date format (YYYY-MM-DD)
- Ensure image file is valid

---

## Support

For issues or questions:
- Check Laravel logs: `storage/logs/laravel.log`
- Verify database tables: `php artisan tinker`
- Test endpoint with cURL or Postman first
- Open an issue on GitHub: https://github.com/goterajohnallanmark/findit/issues
