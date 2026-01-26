# RestReviews - Client API Endpoints

Barcha API endpoint lar `/api` prefiksi bilan boshlanadi.

## 🔐 Autentifikatsiya

API da ikki xil endpoint mavjud:
- **Public (🌐)** - Login talab qilinmaydi
- **Protected (🔒)** - `Authorization: Bearer {token}` header kerak

## 🌍 Tilni tanlash

Barcha API so'rovlarida `Accept-Language` headerini yuborish orqali javob tilini tanlashingiz mumkin:

**Qo'llab-quvvatlanadigan tillar:**
- `uz` - O'zbek tili
- `ru` - Rus tili
- `kk` - Qoraqalpoq tili (default)
- `en` - Ingliz tili

**Header misoli:**
```
Accept-Language: kk
```

Agar header yuborilmasa, avtomatik ravishda qoraqalpoq tili ishlatiladi.

---

## 📍 1. Discovery API - Restoranlarni kashf qilish

### 🌐 GET `/api/restaurants`
Barcha restoranlarni ko'rish (pagination bilan)

**Query Parameters:**
- `page` (optional, integer) - Sahifa raqami
- `per_page` (optional, integer, default: 15) - Har sahifada nechta element
- `category_id` (optional, integer) - Kategoriya bo'yicha filtr
- `city_id` (optional, integer) - Shahar bo'yicha filtr
- `brand_id` (optional, integer) - Brand bo'yicha filtr
- `sort_by` (optional, string: 'rating' | 'latest') - Saralash turi

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "branch_name": "Evos Chilonzor",
      "address": "Chilonzor 9-kvartal",
      "phone": "+998901234567",
      "brand": {
        "id": 1,
        "name": "Evos",
        "logo": "http://..."
      },
      "city": {
        "id": 1,
        "name": "Toshkent"
      },
      "cover_image": "http://...",
      "categories": [...],
      "average_rating": 4.5,
      "reviews_count": 120
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 75
  }
}
```

**Misol:**
```bash
# Barcha restoranlar
GET /api/restaurants

# Fast food kategoriyasidagi restoranlar
GET /api/restaurants?category_id=1

# Toshkent shahridagi restoranlar
GET /api/restaurants?city_id=1

# Reyting bo'yicha saralangan
GET /api/restaurants?sort_by=rating
```

---

### 🌐 GET `/api/restaurants/nearby`
Yaqin atrofdagi restoranlar (Geolokatsiya)

**Query Parameters:**
- `lat` (required, float) - Kenglik (latitude)
- `lng` (required, float) - Uzunlik (longitude)
- `radius` (optional, float, default: 5) - Radius (km)
- `page` (optional, integer)
- `per_page` (optional, integer)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "branch_name": "Evos Chilonzor",
      "distance": 1.5,
      ...
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 2,
    "per_page": 15,
    "total": 20,
    "search_radius_km": 5
  }
}
```

**Misol:**
```bash
# 5km radiusda
GET /api/restaurants/nearby?lat=41.2995&lng=69.2401

# 10km radiusda
GET /api/restaurants/nearby?lat=41.2995&lng=69.2401&radius=10
```

---

### 🌐 GET `/api/restaurants/{id}`
Restoran haqida batafsil ma'lumot

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "branch_name": "Evos Chilonzor",
    "phone": "+998901234567",
    "description": "...",
    "address": "Chilonzor 9-kvartal",
    "latitude": 41.2995,
    "longitude": 69.2401,
    "brand": {
      "id": 1,
      "name": "Evos",
      "logo": "http://...",
      "description": "..."
    },
    "city": {
      "id": 1,
      "name": "Toshkent"
    },
    "categories": [...],
    "images": [
      {
        "id": 1,
        "image_path": "http://...",
        "is_cover": true
      }
    ],
    "operating_hours": [
      {
        "day_of_week": 1,
        "opening_time": "09:00",
        "closing_time": "22:00",
        "is_closed": false
      }
    ],
    "average_rating": 4.5,
    "reviews_count": 120,
    "is_favorited": false
  }
}
```

**Misol:**
```bash
GET /api/restaurants/1
```

---

## 🍔 2. Menu API

### 🌐 GET `/api/restaurants/{id}/menu`
Restoran menyusi (bo'limlar bo'yicha guruhlangan)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Lavash",
      "sort_order": 1,
      "items": [
        {
          "id": 1,
          "name": "Lavash klassik",
          "description": "Go'sht, pomidor, bodring...",
          "image_path": "http://...",
          "base_price": 25000,
          "restaurant_price": 24000,
          "is_available": true
        }
      ]
    }
  ]
}
```

**Misol:**
```bash
GET /api/restaurants/1/menu
```

---

### 🌐 GET `/api/menu-items/{id}`
Taom haqida batafsil

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Lavash klassik",
    "description": "Go'sht, pomidor, bodring, sous",
    "image_path": "http://...",
    "base_price": 25000
  }
}
```

**Misol:**
```bash
GET /api/menu-items/1
```

---

## ⭐ 3. Review API - Sharhlar

### 🌐 GET `/api/restaurants/{id}/reviews`
Restoran sharhlari

**Query Parameters:**
- `page` (optional, integer)
- `per_page` (optional, integer)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "client": {
        "id": 1,
        "full_name": "Alisher Navoiy",
        "image_path": "http://..."
      },
      "restaurant_id": 1,
      "rating": 5,
      "comment": "Juda yoqdi, tavsiya qilaman!",
      "created_at": "2026-01-26 12:00:00",
      "updated_at": "2026-01-26 12:00:00"
    }
  ],
  "statistics": {
    "average_rating": 4.5,
    "total_reviews": 120,
    "rating_distribution": {
      "1": 2,
      "2": 5,
      "3": 15,
      "4": 30,
      "5": 68
    }
  },
  "meta": {
    "current_page": 1,
    "last_page": 8,
    "per_page": 15,
    "total": 120
  }
}
```

**Misol:**
```bash
GET /api/restaurants/1/reviews
```

---

### 🔒 POST `/api/restaurants/{id}/reviews`
Sharh qoldirish yoki yangilash

**Headers:**
```
Authorization: Bearer {token}
```

**Body:**
```json
{
  "rating": 5,
  "comment": "Juda yoqdi, tavsiya qilaman!"
}
```

**Validation:**
- `rating` - majburiy, 1-5 oralig'ida
- `comment` - ixtiyoriy, maksimal 1000 belgi

**Response:**
```json
{
  "success": true,
  "message": "Fikr-mulohaza muvaffaqiyatli saqlandi",
  "data": {
    "id": 1,
    "client": {...},
    "rating": 5,
    "comment": "...",
    "created_at": "2026-01-26 12:00:00"
  }
}
```

**Misol:**
```bash
POST /api/restaurants/1/reviews
Authorization: Bearer eyJ0eXAiOi...

{
  "rating": 5,
  "comment": "Ajoyib restoran!"
}
```

**Muhim:**
- Bir mijoz bitta restoranga faqat bitta sharh qoldirishi mumkin
- Agar sharh mavjud bo'lsa, yangilanadi

---

### 🔒 PUT `/api/reviews/{id}`
Sharhni tahrirlash

**Headers:**
```
Authorization: Bearer {token}
```

**Body:**
```json
{
  "rating": 4,
  "comment": "Yangilangan sharh"
}
```

**Misol:**
```bash
PUT /api/reviews/1
```

---

### 🔒 DELETE `/api/reviews/{id}`
Sharhni o'chirish

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Sharh muvaffaqiyatli o'chirildi"
}
```

**Misol:**
```bash
DELETE /api/reviews/1
```

---

## ❤️ 4. Favorites API - Sevimlilar

### 🔒 GET `/api/favorites`
Sevimli restoranlar ro'yxati

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (optional, integer)
- `per_page` (optional, integer)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "restaurant": {
        "id": 1,
        "branch_name": "Evos Chilonzor",
        "address": "...",
        "brand": {...},
        "cover_image": "http://...",
        "average_rating": 4.5,
        "reviews_count": 120
      },
      "created_at": "2026-01-26 10:00:00"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 2,
    "per_page": 15,
    "total": 25
  }
}
```

**Misol:**
```bash
GET /api/favorites
Authorization: Bearer eyJ0eXAiOi...
```

---

### 🔒 POST `/api/restaurants/{id}/favorite`
Sevimlilar ro'yxatiga qo'shish/o'chirish (Toggle)

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Restoran sevimlilarga qo'shildi",
  "data": {
    "is_favorited": true
  }
}
```

**Misol:**
```bash
# Birinchi marta bosish - qo'shiladi
POST /api/restaurants/1/favorite
Response: { "is_favorited": true, "message": "Restoran sevimlilarga qo'shildi" }

# Ikkinchi marta bosish - o'chiriladi
POST /api/restaurants/1/favorite
Response: { "is_favorited": false, "message": "Restoran sevimlilardan o'chirildi" }
```

---

## 🔍 5. Search API - Qidiruv

### 🌐 GET `/api/search`
Global qidiruv (restoranlar va taomlar)

**Query Parameters:**
- `q` (required, string, min: 2) - Qidiruv so'zi
- `page` (optional, integer)
- `per_page` (optional, integer)

**Response:**
```json
{
  "success": true,
  "data": {
    "restaurants": {
      "data": [
        {
          "id": 1,
          "branch_name": "Evos Chilonzor",
          ...
        }
      ],
      "meta": {
        "current_page": 1,
        "last_page": 2,
        "total": 20
      }
    },
    "menu_items": {
      "data": [
        {
          "id": 1,
          "name": "Lavash klassik",
          "description": "...",
          ...
        }
      ],
      "total": 15
    }
  }
}
```

**Misol:**
```bash
# Restoran va taomlarni qidirish
GET /api/search?q=lavash

# "osh" so'zini qidirish
GET /api/search?q=osh
```

---

## 🔐 6. Authentication API

### 🌐 POST `/api/auth/send-code`
SMS kod yuborish

**Body:**
```json
{
  "phone": "998901234567"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Kod yuborildi"
}
```

---

### 🌐 POST `/api/auth/verify-code`
Kodni tekshirish va tizimga kirish

**Body:**
```json
{
  "phone": "998901234567",
  "code": "1234"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "client": {
      "id": 1,
      "first_name": "Alisher",
      "last_name": "Navoiy",
      "phone": "998901234567"
    },
    "token": "1|abc123..."
  }
}
```

---

### 🔒 GET `/api/auth/me`
Joriy foydalanuvchi ma'lumotlari

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "first_name": "Alisher",
    "last_name": "Navoiy",
    "phone": "998901234567",
    "image_path": "http://..."
  }
}
```

---

### 🔒 POST `/api/auth/logout`
Tizimdan chiqish

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

---

## 📊 Response Format

Barcha API responselar quyidagi formatda qaytadi:

### Success Response
```json
{
  "success": true,
  "data": {...},
  "message": "Optional message"
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field": ["Validation error message"]
  }
}
```

### HTTP Status Codes
- `200` - OK
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

---

## 🌍 Base URL

**Development:**
```
http://localhost/api
```

**Production:**
```
https://yourdomain.com/api
```

---

## 📝 Muhim Eslatmalar

1. **Authentication:**
   - Public endpoint lar uchun token talab qilinmaydi
   - Protected endpoint lar uchun header da Bearer token yuborish kerak
   - Token format: `Authorization: Bearer {token}`

2. **Pagination:**
   - Default: 15 ta element per page
   - Maksimal: 100 ta element per page
   - Response da meta ma'lumotlari qaytadi

3. **Multi-language:**
   - API hozircha O'zbek tilida javob qaytaradi
   - Kelajakda tilni tanlash imkoniyati qo'shiladi

4. **Images:**
   - Barcha rasm URL lari to'liq (absolute) formatda qaytadi
   - Format: `http://yourdomain.com/storage/path/to/image.jpg`

5. **Geolocation:**
   - Latitude: -90 dan +90 gacha
   - Longitude: -180 dan +180 gacha
   - Radius: km da (default 5km, max 50km)

6. **Rating:**
   - Faqat 1 dan 5 gacha ruxsat etilgan
   - Bir mijoz bitta restoranga faqat bitta sharh qoldiradi
   - Sharh yangilansa, avvalgisi o'chiriladi
