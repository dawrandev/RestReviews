# API O'zgarishlari va Yangi Funksiyalar

## 1. Ovqat Massasi (Weight) Maydonini Qo'shish

### Database O'zgarishi
- **Migration**: `2026_01_26_115930_add_weight_grams_to_menu_items_table.php`
- **Jadval**: `menu_items`
- **Yangi maydon**: `weight_grams` (integer, nullable)

### Model O'zgarishi
- **Fayl**: `app/Models/MenuItem.php`
- `weight_grams` maydoni `fillable` arrayga qo'shildi

### API Response O'zgarishi
- **Fayl**: `app/Http/Resources/MenuItemResource.php`
- Menu item ma'lumotlarida `weight_grams` maydoni qaytariladi

### Misol Response:
```json
{
  "id": 1,
  "name": "Big Mac",
  "description": "...",
  "image_path": "...",
  "base_price": 35000,
  "weight_grams": 250,
  "restaurant_price": 40000,
  "is_available": true
}
```

---

## 2. Kategoriya va Reyting bo'yicha Filtrlash

### Endpoint
**GET** `/api/restaurants`

### Yangi Parametrlar
| Parametr | Tip | Tavsif | Misol |
|----------|-----|--------|-------|
| `category_id` | integer | Kategoriya ID | `category_id=1` |
| `min_rating` | float | Minimal reyting | `min_rating=4.0` |
| `max_rating` | float | Maksimal reyting | `max_rating=5.0` |
| `menu_section_id` | integer | Menu bo'limi ID | `menu_section_id=3` |
| `sort_by` | string | Saralash (rating/latest) | `sort_by=rating` |

### Misol So'rovlar

#### 1. Fast Food kategoriyasidagi yuqori reytingli restaranlar (4-5 ball)
```
GET /api/restaurants?category_id=2&min_rating=4.0&max_rating=5.0&sort_by=rating
```

#### 2. Kafelar kategoriyasidagi eng yaxshi restaranlar
```
GET /api/restaurants?category_id=3&min_rating=4.5&sort_by=rating
```

#### 3. Sho'rva bo'limiga ega restaranlar
```
GET /api/restaurants?menu_section_id=1
```

### Response Misol:
```json
{
  "success": true,
  "data": [
    {
      "id": 5,
      "branch_name": "Bellissimo Chilonzor",
      "address": "Chilonzor 12-kvartal",
      "phone": "+998901234567",
      "brand": {
        "id": 2,
        "name": "Bellissimo",
        "logo": "http://example.com/storage/brands/logo.png"
      },
      "city": {
        "id": 1,
        "name": "Toshkent"
      },
      "cover_image": "http://example.com/storage/restaurants/cover.jpg",
      "categories": [
        {
          "id": 2,
          "name": "Fast Food"
        }
      ],
      "average_rating": 4.5,
      "reviews_count": 128
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 68
  }
}
```

---

## 3. Xarita uchun Restaranlar API

### Endpoint
**GET** `/api/restaurants/map`

### Tavsif
Ushbu endpoint xaritada restaranlarni ko'rsatish uchun zarur minimal ma'lumotlarni qaytaradi:
- Restoran ID
- Nomi
- Brand nomi
- Latitude (kenglik)
- Longitude (uzunlik)
- O'rtacha reyting

### Parametrlar
| Parametr | Tip | Tavsif | Majburiy |
|----------|-----|--------|----------|
| `category_id` | integer | Kategoriya filtri | Yo'q |
| `city_id` | integer | Shahar filtri | Yo'q |
| `min_rating` | float | Minimal reyting | Yo'q |
| `max_rating` | float | Maksimal reyting | Yo'q |

### Misol So'rov:
```
GET /api/restaurants/map?category_id=2&min_rating=4.0
```

### Response Misol:
```json
{
  "success": true,
  "data": [
    {
      "id": 5,
      "name": "Bellissimo Chilonzor",
      "brand_name": "Bellissimo",
      "latitude": 41.281835,
      "longitude": 69.222935,
      "average_rating": 4.5
    },
    {
      "id": 8,
      "name": "Bellissimo Sergeli",
      "brand_name": "Bellissimo",
      "latitude": 41.216652,
      "longitude": 69.222935,
      "average_rating": 4.7
    }
  ]
}
```

### Android Map Integratsiya
```kotlin
// Kotlin misol
data class RestaurantMarker(
    val id: Int,
    val name: String,
    val brandName: String,
    val latitude: Double,
    val longitude: Double,
    val averageRating: Double
)

// API dan olish
val response = apiService.getRestaurantsForMap(categoryId = 2, minRating = 4.0)

// Xaritada ko'rsatish
response.data.forEach { restaurant ->
    val position = LatLng(restaurant.latitude, restaurant.longitude)
    val marker = map.addMarker(
        MarkerOptions()
            .position(position)
            .title(restaurant.name)
            .snippet("${restaurant.brandName} • ⭐ ${restaurant.averageRating}")
    )
}
```

---

## 4. Menu Bo'limi bo'yicha Filtrlash

### Endpoint
**GET** `/api/restaurants`

### Parametr
- `menu_section_id` - Menu bo'limining ID si

### Tavsif
Bu parametr orqali faqat ma'lum menu bo'limiga (masalan, sho'rvalar, salatlar) tegishli ovqatlarga ega restaranlarni filtrlash mumkin.

### Misol So'rovlar:

#### Sho'rvalarga ega restaranlar
```
GET /api/restaurants?menu_section_id=1
```

#### Salatlar bo'limiga ega restaranlar
```
GET /api/restaurants?menu_section_id=2
```

#### Ichimliklar va yuqori reytingli restaranlar
```
GET /api/restaurants?menu_section_id=5&min_rating=4.0&sort_by=rating
```

---

## Migration ni Ishga Tushirish

Database o'zgarishlarini qo'llash uchun:

```bash
php artisan migrate
```

Agar migration orqaga qaytarish kerak bo'lsa:

```bash
php artisan migrate:rollback --step=1
```

---

## Barcha Yangi Endpointlar Ro'yxati

1. **GET** `/api/restaurants` - Restaranlar ro'yxati (yangilangan filtrlar bilan)
   - Yangi parametrlar: `menu_section_id`, `min_rating`, `max_rating`

2. **GET** `/api/restaurants/map` - Xarita uchun restaranlar
   - Parametrlar: `category_id`, `city_id`, `min_rating`, `max_rating`

3. **GET** `/api/restaurants/{id}/menu` - Restoran menyusi (weight_grams qo'shilgan)

4. **GET** `/api/menu-items/{id}` - Ovqat ma'lumotlari (weight_grams qo'shilgan)

---

## Test Qilish Misollari

### 1. Yuqori reytingli fast food restaranlarni olish
```bash
curl "http://localhost/api/restaurants?category_id=2&min_rating=4&max_rating=5&sort_by=rating"
```

### 2. Xarita uchun barcha restaranlarni olish
```bash
curl "http://localhost/api/restaurants/map"
```

### 3. Sho'rva bo'limiga ega restaranlarni olish
```bash
curl "http://localhost/api/restaurants?menu_section_id=1"
```

### 4. Kategoriya va menu bo'limi bo'yicha filtrlash
```bash
curl "http://localhost/api/restaurants?category_id=2&menu_section_id=3&min_rating=4"
```

---

## Muhim Eslatmalar

1. **Reyting Filtri**: `min_rating` va `max_rating` faqat sharhlar mavjud bo'lgan restoranlarga ta'sir qiladi. Sharhlari yo'q restaranlar 0 reyting bilan qaytariladi.

2. **Menu Bo'limi Filtri**: `menu_section_id` parametri faqat shu bo'limda hech bo'lmaganda bitta ovqat mavjud bo'lgan restaranlarni qaytaradi.

3. **Xarita Endpoint**: Xarita endpoint paginatsiya qilmaydi - barcha natijalarni bir vaqtning o'zida qaytaradi. Agar juda ko'p restaranlar bo'lsa, qo'shimcha filtrlash kerak.

4. **Ovqat Massasi**: `weight_grams` maydoni nullable, shuning uchun ba'zi ovqatlarda bu qiymat `null` bo'lishi mumkin.

5. **Til (Language)**: Barcha endpointlar `Accept-Language` header orqali til tanlanishini qo'llab-quvvatlaydi:
   - `uz` - O'zbek
   - `ru` - Rus
   - `kk` - Qozoq (default)
   - `en` - Ingliz

---

## Xatoliklar va Debugging

Agar APIlar ishlamasa, quyidagilarni tekshiring:

1. Migration ishga tushganini tekshiring:
```bash
php artisan migrate:status
```

2. Route listni ko'ring:
```bash
php artisan route:list --path=restaurants
```

3. Cache tozalang:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---

Barcha o'zgarishlar tayyor va ishlatishga tayyor! 🚀
