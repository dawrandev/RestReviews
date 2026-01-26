# RestReviews API - Quick Start Guide

## ⚡ Tezkor Boshlash

### 1. Database Migration
```bash
php artisan migrate
```

### 2. API ni ishga tushirish
```bash
php artisan serve
```

API manzili: `http://localhost:8000`

---

## 🧪 Test Qilish (Postman/Thunder Client)

### Authentifikatsiya

#### 1. SMS kod yuborish
```http
POST http://localhost:8000/api/auth/send-code
Content-Type: application/json

{
  "phone": "998901234567"
}
```

#### 2. Kodni tekshirish va token olish
```http
POST http://localhost:8000/api/auth/verify-code
Content-Type: application/json

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
    "token": "1|abc123...",
    "client": {...}
  }
}
```

**Token ni saqlang! Keyingi so'rovlar uchun kerak bo'ladi.**

---

### Public API (Tokensiz)

#### 3. Barcha restoranlarni olish
```http
GET http://localhost:8000/api/restaurants
```

#### 4. Yaqin atrofdagi restoranlar
```http
GET http://localhost:8000/api/restaurants/nearby?lat=41.2995&lng=69.2401&radius=5
```

#### 5. Restoran haqida ma'lumot
```http
GET http://localhost:8000/api/restaurants/1
```

#### 6. Restoran menyusi
```http
GET http://localhost:8000/api/restaurants/1/menu
```

#### 7. Restoran sharhlari
```http
GET http://localhost:8000/api/restaurants/1/reviews
```

#### 8. Qidiruv
```http
GET http://localhost:8000/api/search?q=lavash
```

---

### Protected API (Token bilan)

**Barcha protected API uchun header qo'shing:**
```http
Authorization: Bearer 1|abc123...
```

#### 9. Sharh qoldirish
```http
POST http://localhost:8000/api/restaurants/1/reviews
Authorization: Bearer 1|abc123...
Content-Type: application/json

{
  "rating": 5,
  "comment": "Juda yoqdi!"
}
```

#### 10. Sevimlilar ro'yxatiga qo'shish
```http
POST http://localhost:8000/api/restaurants/1/favorite
Authorization: Bearer 1|abc123...
```

#### 11. Sevimlilar ro'yxati
```http
GET http://localhost:8000/api/favorites
Authorization: Bearer 1|abc123...
```

---

## 📦 Postman Collection

### Environment Variables
```json
{
  "base_url": "http://localhost:8000",
  "api_url": "{{base_url}}/api",
  "token": ""
}
```

### Collection Example

**1. Auth Folder:**
- Send Code (POST `{{api_url}}/auth/send-code`)
- Verify Code (POST `{{api_url}}/auth/verify-code`)
- Get Me (GET `{{api_url}}/auth/me`)
- Logout (POST `{{api_url}}/auth/logout`)

**2. Discovery Folder:**
- Get All Restaurants (GET `{{api_url}}/restaurants`)
- Get Nearby Restaurants (GET `{{api_url}}/restaurants/nearby`)
- Get Restaurant Details (GET `{{api_url}}/restaurants/1`)

**3. Menu Folder:**
- Get Restaurant Menu (GET `{{api_url}}/restaurants/1/menu`)
- Get Menu Item (GET `{{api_url}}/menu-items/1`)

**4. Reviews Folder:**
- Get Restaurant Reviews (GET `{{api_url}}/restaurants/1/reviews`)
- Create/Update Review (POST `{{api_url}}/restaurants/1/reviews`)
- Update Review (PUT `{{api_url}}/reviews/1`)
- Delete Review (DELETE `{{api_url}}/reviews/1`)

**5. Favorites Folder:**
- Get My Favorites (GET `{{api_url}}/favorites`)
- Toggle Favorite (POST `{{api_url}}/restaurants/1/favorite`)

**6. Search Folder:**
- Global Search (GET `{{api_url}}/search?q=lavash`)

---

## 🔧 Development Commands

```bash
# Migrationlarni qaytarish
php artisan migrate:rollback

# Fresh migration (barcha ma'lumotlarni o'chiradi)
php artisan migrate:fresh

# Seeder bilan birga
php artisan migrate:fresh --seed

# Cache tozalash
php artisan optimize:clear

# Route list
php artisan route:list --path=api

# Model yaratish
php artisan make:model ModelName -m

# Controller yaratish
php artisan make:controller Api/ControllerName

# Request yaratish
php artisan make:request StoreRequestName

# Repository yaratish (manual)
# app/Repositories/RepositoryName.php

# Service yaratish (manual)
# app/Services/ServiceName.php
```

---

## 🎯 Clean Architecture Pattern

```
Controller -> Service -> Repository -> Model
     ↓           ↓           ↓
 Request    Business     Database
Validation   Logic       Queries
```

### Example:

```php
// 1. Request Validation
StoreReviewRequest validates input

// 2. Controller receives request
ReviewController->store(StoreReviewRequest $request)

// 3. Service handles business logic
ReviewService->createOrUpdateReview($clientId, $restaurantId, $data)

// 4. Repository handles database
ReviewRepository->create($data) or update($review, $data)

// 5. Model interacts with database
Review::create($data)

// 6. Resource formats response
ReviewResource::make($review)
```

---

## 📊 Database Schema

### reviews table
- `id` (bigint, PK)
- `client_id` (FK to clients)
- `restaurant_id` (FK to restaurants)
- `rating` (tinyint, 1-5)
- `comment` (text, nullable)
- `created_at`, `updated_at`
- **UNIQUE**: (client_id, restaurant_id)

### favorites table
- `id` (bigint, PK)
- `client_id` (FK to clients)
- `restaurant_id` (FK to restaurants)
- `created_at`, `updated_at`
- **UNIQUE**: (client_id, restaurant_id)

---

## 🚀 API Endpoints Summary

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/auth/send-code` | ❌ | SMS kod yuborish |
| POST | `/api/auth/verify-code` | ❌ | Kodni tekshirish |
| GET | `/api/auth/me` | ✅ | Profil ma'lumotlari |
| POST | `/api/auth/logout` | ✅ | Chiqish |
| GET | `/api/restaurants` | ❌ | Barcha restoranlar |
| GET | `/api/restaurants/nearby` | ❌ | Yaqin restoranlar |
| GET | `/api/restaurants/{id}` | ❌ | Restoran ma'lumoti |
| GET | `/api/restaurants/{id}/menu` | ❌ | Restoran menyusi |
| GET | `/api/restaurants/{id}/reviews` | ❌ | Restoran sharhlari |
| POST | `/api/restaurants/{id}/reviews` | ✅ | Sharh qoldirish |
| PUT | `/api/reviews/{id}` | ✅ | Sharhni yangilash |
| DELETE | `/api/reviews/{id}` | ✅ | Sharhni o'chirish |
| GET | `/api/favorites` | ✅ | Sevimlilar ro'yxati |
| POST | `/api/restaurants/{id}/favorite` | ✅ | Sevimlilar toggle |
| GET | `/api/search` | ❌ | Global qidiruv |
| GET | `/api/menu-items/{id}` | ❌ | Taom ma'lumoti |

**Jami:** 16 ta endpoint

---

## ⚠️ Muhim Eslatmalar

1. **Authentication:**
   - Test rejimda har doim `1234` kodi ishlaydi
   - Production da Eskiz SMS service ishlatiladi
   - Token vaqti: 60 kun

2. **Rate Limiting:**
   - Default: 60 ta so'rov/minut
   - Middleware: `throttle:api`

3. **Pagination:**
   - Default: 15 items per page
   - Max: 100 items per page
   - Custom: `?per_page=25`

4. **CORS:**
   - Barcha originlarga ruxsat
   - Development uchun sozlangan
   - Production da o'zgartiring

5. **Validation:**
   - Barcha so'rovlar FormRequest orqali validate qilinadi
   - Xatolar 422 status code bilan qaytadi

---

## 📚 Documentation

- API Endpoints: `API_ENDPOINTS.md`
- Clean Architecture: Mavjud pattern asosida qurilgan
- OpenAPI/Swagger: `http://localhost:8000/api/documentation`

---

## 🐛 Debugging

```bash
# Log fayllarni ko'rish
tail -f storage/logs/laravel.log

# Query logging
DB::enableQueryLog();
// ... your code
dd(DB::getQueryLog());

# Debug mode
.env: APP_DEBUG=true
```

---

## ✅ Testing Checklist

- [ ] SMS kod yuborish va verify qilish
- [ ] Restoranlarni olish (pagination)
- [ ] Yaqin restoranlarni olish (geolocation)
- [ ] Restoran detallari
- [ ] Restoran menyusi
- [ ] Sharh qoldirish (auth required)
- [ ] Sharh ko'rish
- [ ] Sevimlilar qo'shish/o'chirish (auth required)
- [ ] Sevimlilar ro'yxati
- [ ] Qidiruv (restaurants + menu items)
- [ ] Token bilan API ga kirish
- [ ] Token siz himoyalangan API ga kirishda 401 error

---

## 📞 Support

Muammolar yuzaga kelsa:
1. `php artisan optimize:clear` - Cache tozalash
2. `composer dump-autoload` - Autoload yangilash
3. Logs tekshirish: `storage/logs/laravel.log`
4. Database tekshirish: PHPMyAdmin yoki TablePlus

---

**API tayyor! Omad! 🚀**
