# 🚀 Quick Setup Guide - Sistem Reservasi Klinik

## Langkah Cepat Setup

### 1. Install Dependencies
```powershell
composer install
npm install
```

### 2. Setup Database
```powershell
# Copy .env
cp .env.example .env

# Generate app key
php artisan key:generate

# Edit .env - sesuaikan database config
# DB_DATABASE=reservasi_klinik
# DB_USERNAME=root
# DB_PASSWORD=

# Run migrations
php artisan migrate

# Seed data awal
php artisan db:seed
```

### 3. Setup WhatsApp API

**Edit file `.env`, tambahkan:**

```env
# Fonnte (Recommended)
WHATSAPP_API_URL=https://api.fonnte.com/send
WHATSAPP_API_KEY=YOUR_FONNTE_API_KEY

# Atau Wablas
# WHATSAPP_API_URL=https://wablas.com/api/send-message
# WHATSAPP_API_KEY=YOUR_WABLAS_TOKEN
```

**Cara daftar Fonnte:**
1. Buka https://fonnte.com
2. Daftar akun gratis
3. Connect WhatsApp dengan scan QR
4. Copy API Key dari dashboard
5. Paste ke `.env`

### 4. Storage & Assets
```powershell
# Create storage link
php artisan storage:link

# Build frontend
npm run build
```

### 5. Run Server
```powershell
php artisan serve
```

Buka browser: http://127.0.0.1:8000

### 6. Login

**Admin:**
- URL: http://127.0.0.1:8000/login
- Username: `admin`
- Password: `password`

**Customer:**
- Username: `customer`
- Password: `password`

## Testing Fitur Utama

### A. Test Booking (sebagai Customer)
1. Login sebagai customer
2. Buat booking → pilih treatment → tanggal → jam → dokter
3. Jika booking < 7 hari: langsung approved
4. Jika booking >= 7 hari: butuh DP

### B. Test DP Management (sebagai Admin)
1. Login sebagai admin
2. Menu Deposits → Lihat pending deposits
3. Approve/Reject deposit
4. Notifikasi WhatsApp akan terkirim (jika API key valid)

### C. Test Auto-Expire (Manual)
```powershell
# Run command manual
php artisan deposits:expire
```

### D. Test Reminder (Manual)
```powershell
php artisan bookings:send-reminders
```

## Setup Scheduled Tasks (Production)

### Windows (Task Scheduler)
1. Buka Task Scheduler
2. Create Basic Task
3. Trigger: Daily
4. Action: Start a program
5. Program: `C:\xampp\php\php.exe`
6. Arguments: `C:\xampp\htdocs\Reservasi\artisan schedule:run`
7. Repeat every: 1 minute

### Linux/Mac (Crontab)
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

## Struktur Menu Admin

```
Admin Panel
├── Dashboard (Stats & Overview)
├── Master Data
│   ├── Treatments (CRUD)
│   ├── Doctors (CRUD)
│   └── Doctor Schedules
├── Bookings
│   ├── List Bookings
│   ├── Create Manual (dari WA)
│   └── Manage (reschedule, cancel, complete)
├── Deposits
│   ├── Pending (butuh approval)
│   └── History
├── Vouchers
│   ├── List & Settings
│   └── Usage Statistics
├── Members
│   ├── List Members
│   └── Activate/Deactivate
└── Feedbacks
    └── Manage Reviews
```

## Troubleshooting

### Error: Class not found
```powershell
composer dump-autoload
php artisan optimize:clear
```

### Error: Migration
```powershell
php artisan migrate:fresh --seed
```

### Error: Storage
```powershell
php artisan storage:link
```

### WhatsApp tidak terkirim
- Cek API key di `.env`
- Cek saldo/quota di dashboard Fonnte/Wablas
- Cek log: `storage/logs/laravel.log`

## Next Steps

### Frontend Development
Backend sudah siap! Tinggal buat views:

1. **Landing Page** (`resources/views/landing/`)
   - index.blade.php
   - treatments.blade.php

2. **Auth Pages** (`resources/views/auth/`)
   - register.blade.php (dengan OTP flow)
   - login.blade.php
   - forgot-password.blade.php

3. **Customer Area** (`resources/views/customer/`)
   - dashboard.blade.php
   - booking/create.blade.php (dengan slot picker)
   - booking/show.blade.php (detail + upload DP)

4. **Admin Panel** (`resources/views/admin/`)
   - dashboard.blade.php
   - treatments/, doctors/, bookings/, dll

**Gunakan Tailwind CSS** (sudah installed):
```html
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold">Hello!</h1>
</div>
```

### Integrasi Payment Gateway (Opsional)
Jika ingin tambah payment gateway (Midtrans, Xendit, dll):
1. Install package
2. Update `BookingService`
3. Tambah callback handler
4. Update deposit flow

## Support

Ada masalah? Check:
- Dokumentasi lengkap: `README_SISTEM.md`
- Laravel docs: https://laravel.com/docs
- API docs (Fonnte): https://fonnte.com/api

---

**Happy Coding! 🎉**
