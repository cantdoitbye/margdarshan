# 🆔 Tutor ID Card Backend - Setup Instructions

## ✅ Files Created

### 1. Migration
- `database/migrations/2026_01_28_000001_add_tutor_id_code_to_users.php`

### 2. Service
- `app/Services/QrCodeService.php`

### 3. Controllers
- `app/Http/Controllers/Api/Tutor/TutorIdCardController.php`
- `app/Http/Controllers/Api/PublicTutorController.php`

### 4. Routes
- Updated `routes/api.php`

---

## 🚀 Setup Steps

### Step 1: Install QR Code Package
```bash
cd tutor_backend
composer require simplesoftwareio/simple-qrcode
```

### Step 2: Run Migration
```bash
php artisan migrate
```

This will add the following columns to the `users` table:
- `tutor_id_code` - Unique tutor ID (e.g., PRIEDU-2026-A1B2C3)
- `qr_code_generated_at` - Timestamp when QR was first generated
- `qr_access_count` - Number of times QR was scanned
- `qr_last_accessed_at` - Last scan timestamp

### Step 3: Configure Frontend URL
Add to `.env`:
```env
FRONTEND_URL=https://priedu.com
# Or for local development:
# FRONTEND_URL=http://localhost:3000
```

### Step 4: Clear Cache
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

---

## 📡 API Endpoints

### For Tutor App (Authenticated)

#### 1. Get/Generate ID Card
```http
GET /api/tutor/id-card
Authorization: Bearer {token}

Response:
{
  "success": true,
  "data": {
    "tutor_id_code": "PRIEDU-2026-A1B2C3",
    "profile_url": "https://priedu.com/tutor/PRIEDU-2026-A1B2C3",
    "qr_code_data_url": "data:image/png;base64,...",
    "generated_at": "2026-01-28T10:00:00Z",
    "tutor_info": {
      "name": "John Doe",
      "photo": "...",
      "tutor_type": "academic",
      "subjects": ["Mathematics", "Physics"],
      "experience_years": 5,
      "education": "B.Tech",
      "status": "active",
      "verified": true
    }
  }
}
```

#### 2. Regenerate QR Code
```http
POST /api/tutor/id-card/regenerate
Authorization: Bearer {token}

Response:
{
  "success": true,
  "message": "QR code regenerated successfully",
  "data": {
    "tutor_id_code": "PRIEDU-2026-X9Y8Z7"
  }
}
```

#### 3. Get Access Statistics
```http
GET /api/tutor/id-card/stats
Authorization: Bearer {token}

Response:
{
  "success": true,
  "data": {
    "total_scans": 45,
    "last_scanned_at": "2026-01-28T15:30:00Z",
    "generated_at": "2026-01-20T10:00:00Z"
  }
}
```

### For Web App (Public)

#### Get Public Tutor Profile
```http
GET /api/public/tutor/{tutorIdCode}

Example: GET /api/public/tutor/PRIEDU-2026-A1B2C3

Response:
{
  "success": true,
  "data": {
    "tutor_id_code": "PRIEDU-2026-A1B2C3",
    "name": "John Doe",
    "profile_photo": "https://...",
    "tutor_type": "academic",
    "subjects": ["Mathematics", "Physics"],
    "qualifications": [
      {
        "board": "CBSE",
        "class": "Class 10",
        "subject": "Mathematics"
      }
    ],
    "education": "B.Tech in Computer Science",
    "experience_years": 5,
    "bio": "Experienced tutor...",
    "hourly_rate": 500,
    "service_location": "Delhi NCR",
    "rating": 4.8,
    "review_count": 120,
    "verified": true,
    "status": "active",
    "verification_badges": {
      "identity_verified": true,
      "documents_verified": true,
      "background_check": true
    }
  }
}
```

---

## 🔐 Security Features

### 1. Unique ID Generation
- Format: `PRIEDU-{YEAR}-{RANDOM-6-CHARS}`
- Checks for uniqueness before saving
- Uses cryptographically secure random generation

### 2. Access Logging
- Tracks every QR scan
- Logs IP address and user agent
- Stores timestamp and access count

### 3. Status Validation
- Only active/approved tutors are shown
- Inactive profiles return 403 error
- Validates tutor ID code format

### 4. Rate Limiting
Add to `app/Http/Kernel.php`:
```php
'api' => [
    'throttle:60,1', // 60 requests per minute
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

---

## 🧪 Testing

### Test QR Generation
```bash
# Using curl
curl -X GET http://localhost:8000/api/tutor/id-card \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

### Test Public Profile
```bash
curl -X GET http://localhost:8000/api/public/tutor/PRIEDU-2026-A1B2C3 \
  -H "Accept: application/json"
```

### Test with Postman
1. Import the API endpoints
2. Set Authorization header with tutor token
3. Test GET /api/tutor/id-card
4. Copy the tutor_id_code from response
5. Test GET /api/public/tutor/{tutor_id_code} (no auth needed)

---

## 📊 Database Schema

### Users Table (Updated)
```sql
users
├── id
├── name
├── email
├── role
├── status
├── tutor_id_code (NEW) - VARCHAR(20) UNIQUE
├── qr_code_generated_at (NEW) - TIMESTAMP
├── qr_access_count (NEW) - INTEGER
└── qr_last_accessed_at (NEW) - TIMESTAMP
```

---

## 🐛 Troubleshooting

### Issue: QR Code Package Not Found
```bash
composer require simplesoftwareio/simple-qrcode
php artisan config:clear
```

### Issue: Migration Fails
```bash
# Check if columns already exist
php artisan migrate:status

# Rollback if needed
php artisan migrate:rollback --step=1

# Re-run migration
php artisan migrate
```

### Issue: QR Code Not Generating
- Check if GD or Imagick extension is installed
- Verify QrCode facade is registered
- Check logs: `storage/logs/laravel.log`

### Issue: Public Profile Returns 404
- Verify tutor_id_code exists in database
- Check if tutor status is 'active' or 'approved'
- Verify route is registered: `php artisan route:list | grep public/tutor`

---

## ✅ Verification Checklist

- [ ] QR code package installed
- [ ] Migration run successfully
- [ ] Frontend URL configured in .env
- [ ] Routes registered (check with `php artisan route:list`)
- [ ] Test ID card generation endpoint
- [ ] Test public profile endpoint
- [ ] Verify QR code image is generated
- [ ] Check access logging works
- [ ] Test with inactive tutor (should fail)
- [ ] Test with invalid tutor ID code (should fail)

---

## 🎉 Success!

Backend is now ready for Tutor ID Card feature!

**Next Steps:**
1. Test all endpoints with Postman
2. Implement mobile app screens
3. Create web app public profile page
4. Deploy to production

---

**Created:** January 28, 2026  
**Status:** ✅ Ready for Testing
