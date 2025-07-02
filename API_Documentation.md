# News Aggregator API Documentation

## Overview

The News Aggregator API is a RESTful service built with Laravel that provides comprehensive news management functionality. It includes user authentication, news article management, categorization, and personalized news preferences.

**Base URL:** `http://localhost:8086/api/v1`

**Authentication:** Bearer Token (Laravel Sanctum)

---

## Table of Contents

1. [Authentication](#authentication)
2. [Articles](#articles)
3. [Categories](#categories)
4. [Publishers](#publishers)
5. [Authors](#authors)
6. [User Preferences](#user-preferences)
7. [Testing](#testing)

---

## Authentication

### Register User
**POST** `/auth/register`

Register a new user account.

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john.doe@example.com",
    "phone_no": "+1234567890",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response:**
```json
{
    "status": "success",
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john.doe@example.com",
            "phone_no": "+1234567890",
            "created_at": "2025-07-02T10:00:00.000000Z"
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

### Login User
**POST** `/auth/login`

Authenticate user and get access token.

**Request Body:**
```json
{
    "email": "john.doe@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "status": "success",
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john.doe@example.com"
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

### Logout User
**POST** `/auth/logout`

**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
    "status": "success",
    "message": "Logged out successfully"
}
```

---

## Articles

### Get All Articles
**GET** `/articles`

Get all news articles with optional filtering and pagination.

**Query Parameters:**
- `page` (optional): Page number for pagination
- `search` (optional): Search in title and content
- `category_id` (optional): Filter by category ID
- `publisher_id` (optional): Filter by publisher ID
- `author_id` (optional): Filter by author ID
- `keyword` (optional): Filter by keyword

**Example:**
```bash
GET /articles?page=1&search=technology&category_id=1&publisher_id=1&author_id=1&keyword=AI
```

**Response:**
```json
{
    "status": "success",
    "message": "Articles retrieved successfully",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "title": "Latest Technology News",
                "content": "Article content...",
                "slug": "latest-technology-news",
                "url": "https://example.com/article",
                "published_at": "2025-07-02T10:00:00.000000Z",
                "author": {
                    "id": 1,
                    "name": "John Smith"
                },
                "publisher": {
                    "id": 1,
                    "name": "Tech News"
                },
                "newsCategory": {
                    "id": 1,
                    "name": "Technology"
                },
                "keywords": [
                    {
                        "id": 1,
                        "keyword": "AI"
                    }
                ]
            }
        ],
        "total": 100,
        "per_page": 15
    }
}
```

### Get Article by ID
**GET** `/articles/{id}`

**Response:**
```json
{
    "status": "success",
    "message": "Article retrieved successfully",
    "data": {
        "id": 1,
        "title": "Latest Technology News",
        "content": "Article content...",
        "slug": "latest-technology-news",
        "url": "https://example.com/article",
        "published_at": "2025-07-02T10:00:00.000000Z",
        "author": {
            "id": 1,
            "name": "John Smith"
        },
        "publisher": {
            "id": 1,
            "name": "Tech News"
        },
        "newsCategory": {
            "id": 1,
            "name": "Technology"
        },
        "keywords": [
            {
                "id": 1,
                "keyword": "AI"
            }
        ]
    }
}
```

### Get Article by Slug
**GET** `/articles/slug/{slug}`

### Get Related Articles
**GET** `/articles/{id}/related`

### Create Article
**POST** `/articles`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "title": "New Article Title",
    "content": "Article content...",
    "url": "https://example.com/article",
    "author_id": 1,
    "source_id": 1,
    "category_id": 1,
    "published_at": "2025-07-02T10:00:00.000000Z",
    "keywords": ["AI", "Technology", "Innovation"]
}
```

### Update Article
**PUT** `/articles/{id}`

**Headers:** `Authorization: Bearer {token}`

### Delete Article
**DELETE** `/articles/{id}`

**Headers:** `Authorization: Bearer {token}`

---

## Categories

### Get All Categories
**GET** `/categories`

**Response:**
```json
{
    "status": "success",
    "message": "Categories retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "Technology",
            "description": "Technology related news",
            "created_at": "2025-07-02T10:00:00.000000Z"
        }
    ]
}
```

### Get Category by ID
**GET** `/categories/{id}`

### Get Articles by Category
**GET** `/categories/{id}/articles`

### Create Category
**POST** `/categories`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "name": "New Category",
    "description": "Category description"
}
```

### Update Category
**PUT** `/categories/{id}`

**Headers:** `Authorization: Bearer {token}`

### Delete Category
**DELETE** `/categories/{id}`

**Headers:** `Authorization: Bearer {token}`

---

## Publishers

### Get All Publishers
**GET** `/publishers`

**Response:**
```json
{
    "status": "success",
    "message": "Publishers retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "Tech News",
            "website": "https://technews.com",
            "description": "Technology news publisher",
            "created_at": "2025-07-02T10:00:00.000000Z"
        }
    ]
}
```

### Get Publisher by ID
**GET** `/publishers/{id}`

### Get Articles by Publisher
**GET** `/publishers/{id}/articles`

### Create Publisher
**POST** `/publishers`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "name": "New Publisher",
    "website": "https://newpublisher.com",
    "description": "Publisher description"
}
```

### Update Publisher
**PUT** `/publishers/{id}`

**Headers:** `Authorization: Bearer {token}`

### Delete Publisher
**DELETE** `/publishers/{id}`

**Headers:** `Authorization: Bearer {token}`

---

## Authors

### Get All Authors
**GET** `/authors`

**Response:**
```json
{
    "status": "success",
    "message": "Authors retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "John Smith",
            "email": "john@example.com",
            "bio": "Technology journalist",
            "created_at": "2025-07-02T10:00:00.000000Z"
        }
    ]
}
```

### Get Author by ID
**GET** `/authors/{id}`

### Get Articles by Author
**GET** `/authors/{id}/articles`

### Create Author
**POST** `/authors`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "name": "Jane Doe",
    "email": "jane@example.com",
    "bio": "Author bio"
}
```

### Update Author
**PUT** `/authors/{id}`

**Headers:** `Authorization: Bearer {token}`

---

## User Preferences

### Get All Preferences
**GET** `/preferences`

**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
    "status": "success",
    "message": "User preferences retrieved successfully",
    "data": {
        "categories": [
            {
                "id": 1,
                "user_id": 1,
                "preference_type": "category",
                "preference_id": 1,
                "category": {
                    "id": 1,
                    "name": "Technology"
                }
            }
        ],
        "sources": [
            {
                "id": 2,
                "user_id": 1,
                "preference_type": "source",
                "preference_id": 1,
                "publisher": {
                    "id": 1,
                    "name": "Tech News"
                }
            }
        ],
        "authors": [
            {
                "id": 3,
                "user_id": 1,
                "preference_type": "author",
                "preference_id": 1,
                "author": {
                    "id": 1,
                    "name": "John Smith"
                }
            }
        ]
    }
}
```

### Create Preference
**POST** `/preferences`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "preference_type": "category",
    "preference_id": 1
}
```

**Preference Types:**
- `category`: News category preference
- `source`: News source/publisher preference
- `author`: Author preference

### Bulk Create Preferences
**POST** `/preferences/bulk`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "preferences": [
        {
            "preference_type": "category",
            "preference_id": 1
        },
        {
            "preference_type": "source",
            "preference_id": 2
        },
        {
            "preference_type": "author",
            "preference_id": 3
        }
    ]
}
```

### Get Preferred Articles
**GET** `/preferences/articles`

**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
- `is_read` (optional): Filter by read status (true/false)
- `limit` (optional): Number of articles per page (default: 15)
- `days` (optional): Filter articles from last N days
- `start_date` (optional): Filter articles from this date (YYYY-MM-DD)
- `end_date` (optional): Filter articles until this date (YYYY-MM-DD)
- `author_id` (optional): Filter by author ID
- `category_id` (optional): Filter by category ID
- `publisher_id` (optional): Filter by publisher/source ID
- `search` (optional): Search in article title and content

**Example:**
```bash
GET /preferences/articles?is_read=false&limit=15&days=7&start_date=2025-01-01&end_date=2025-12-31&author_id=1&category_id=1&publisher_id=1&search=technology
```

**Response:**
```json
{
    "status": "success",
    "message": "Preferred articles retrieved successfully",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "title": "Latest Technology News",
                "content": "Article content...",
                "is_read": false,
                "preferred_at": "2025-07-02T10:00:00.000000Z",
                "author": {
                    "id": 1,
                    "name": "John Smith"
                },
                "publisher": {
                    "id": 1,
                    "name": "Tech News"
                },
                "newsCategory": {
                    "id": 1,
                    "name": "Technology"
                }
            }
        ],
        "total": 50,
        "per_page": 15
    }
}
```

### Mark Article as Read
**POST** `/preferences/articles/mark-read`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "article_id": 1,
    "is_read": true
}
```

### Get Preference by ID
**GET** `/preferences/{id}`

**Headers:** `Authorization: Bearer {token}`

### Update Preference
**PUT** `/preferences/{id}`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "preference_type": "source",
    "preference_id": 2
}
```

### Delete Preference
**DELETE** `/preferences/{id}`

**Headers:** `Authorization: Bearer {token}`

### Clear All Preferences
**DELETE** `/preferences/clear`

**Headers:** `Authorization: Bearer {token}`

---

## Testing

### Test Connection
**GET** `/test`

**Response:**
```json
{
    "message": "General test"
}
```

### Test Authentication
**GET** `/test-auth`

**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
    "message": "This should require auth"
}
```

---

## Error Responses

### Validation Error (422)
```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password field is required."]
    }
}
```

### Authentication Error (401)
```json
{
    "status": "error",
    "message": "You are not authenticated to access this resource."
}
```

### Not Found Error (404)
```json
{
    "status": "error",
    "message": "The requested resource was not found."
}
```

### Server Error (500)
```json
{
    "status": "error",
    "message": "Internal server error occurred."
}
```

---

## Authentication Flow

1. **Register** a new user account using `/auth/register`
2. **Login** with credentials using `/auth/login` to get an access token
3. **Include** the token in the `Authorization` header for protected endpoints:
   ```
   Authorization: Bearer {your_token_here}
   ```
4. **Logout** using `/auth/logout` to invalidate the token

---

## Rate Limiting

The API implements rate limiting to prevent abuse. Please respect the rate limits and implement appropriate retry logic in your applications.

---

## Support

For API support and questions, please refer to the project documentation or contact the development team.

---

## Version History

- **v1.0.0**: Initial API release with basic CRUD operations
- **v1.1.0**: Added user preferences and preferred articles functionality
- **v1.2.0**: Enhanced filtering capabilities for preferred articles 