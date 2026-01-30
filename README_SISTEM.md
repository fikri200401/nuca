# Sistem Reservasi Klinik Kecantikan

Sistem web untuk manajemen booking treatment klinik kecantikan dengan fitur lengkap seperti OTP WhatsApp, deposit management, member system, voucher, dan before-after photos.

## 📋 Fitur Utama

### 1. **Authentication Berbasis WhatsApp**
- Registrasi menggunakan nomor WhatsApp + OTP
- Login dengan WhatsApp / Username / Member Number
- Reset password via OTP WhatsApp

### 2. **Booking System**
- Pilih treatment dengan durasi berbeda
- Auto-blocking slot sesuai durasi treatment
- Pilih dokter yang available
- Auto-approve untuk booking hari yang sama
- DP wajib untuk booking 7 hari ke depan (minimal Rp 50.000)
- Auto-expire deposit setelah 24 jam

### 3. **Member & Discount**
- Member mendapat diskon 10% (configurable)
- Voucher bulanan dengan syarat transaksi
- Reward berdasarkan nominal transaksi

### 4. **Notification System**
- Notifikasi WhatsApp otomatis untuk:
  - Konfirmasi booking
  - Status DP (waiting, approved, rejected, expired)
  - Reminder H-1 sebelum appointment

### 5. **Admin Panel**
- Dashboard & statistik
- Master data: Treatment, Dokter, Jadwal
- Booking management (input manual dari WhatsApp)
- DP verification
- Member management
- Voucher settings
- Feedback management
- Upload before-after photos
- No-show notes (admin only)

### 6. **Before-After Photos**
- Upload oleh admin per booking
- Hanya bisa dilihat oleh customer terkait
- Owner tidak otomatis punya akses

## 🚀 Installation

### Prerequisites
- PHP >= 8.2
- Composer
- MySQL/PostgreSQL
- Node.js & NPM (untuk frontend assets)

### Steps

1. **Clone & Install Dependencies**
```bash
cd c:\xampp\htdocs\Reservasi
composer install
npm install
```

2. **Environment Setup**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Database Configuration**

Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=reservasi_klinik
DB_USERNAME=root
DB_PASSWORD=
```

4. **WhatsApp API Configuration**

Tambahkan ke `.env`:
```env
# Gunakan Fonnte
WHATSAPP_API_URL=https://api.fonnte.com/send
WHATSAPP_API_KEY=your_fonnte_api_key

# Atau gunakan Wablas
# WHATSAPP_API_URL=https://wablas.com/api/send-message
# WHATSAPP_API_KEY=your_wablas_token
```

**Cara mendapatkan API Key:**

**Fonnte:**
1. Daftar di [https://fonnte.com](https://fonnte.com)
2. Connect nomor WhatsApp Anda
3. Dapatkan API Key dari dashboard

**Wablas:**
1. Daftar di [https://wablas.com](https://wablas.com)
2. Beli paket yang sesuai
3. Dapatkan token dari dashboard

5. **Run Migrations & Seeders**
```bash
php artisan migrate
php artisan db:seed
```

6. **Storage Link**
```bash
php artisan storage:link
```

7. **Build Frontend Assets**
```bash
npm run build
```

8. **Run Development Server**
```bash
php artisan serve
```

9. **Setup Scheduled Tasks**

Untuk auto-expire deposits dan kirim reminder, tambahkan ke cron (Linux/Mac) atau Task Scheduler (Windows):

**Linux/Mac crontab:**
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

**Windows Task Scheduler:**
Buat task yang menjalankan command berikut setiap menit:
```
C:\xampp\php\php.exe C:\xampp\htdocs\Reservasi\artisan schedule:run
```

Atau untuk development, jalankan manual:
```bash
php artisan deposits:expire
php artisan bookings:send-reminders
```

## 👥 Default Accounts

Setelah seeding, tersedia akun default:

### Admin
- Username: `admin`
- WhatsApp: `081234567890`
- Password: `password`

### Owner
- Username: `owner`
- WhatsApp: `081234567891`
- Password: `password`

### Customer (Demo Member)
- Username: `customer`
- WhatsApp: `081234567892`
- Member Number: `MBR-DEMO001`
- Password: `password`

## 📱 Status Booking

| Status | Deskripsi |
|--------|-----------|
| `auto_approved` | Booking hari yang sama, langsung approved |
| `waiting_deposit` | Booking jauh hari, menunggu DP |
| `deposit_confirmed` | DP sudah diverifikasi dan approved |
| `deposit_rejected` | DP ditolak oleh admin |
| `expired` | Melewati batas 24 jam tanpa konfirmasi DP |
| `completed` | Treatment sudah selesai |
| `cancelled` | Dibatalkan oleh admin |

## 🔄 Workflow Booking

### Booking Hari yang Sama
1. Customer pilih treatment → tanggal → jam → dokter
2. Sistem create booking dengan status `auto_approved`
3. Kirim notifikasi WhatsApp konfirmasi
4. Treatment selesai → status `completed`
5. Customer bisa kasih feedback
6. Admin upload foto before-after

### Booking 7 Hari Ke Depan
1. Customer pilih treatment → tanggal → jam → dokter
2. Sistem create booking dengan status `waiting_deposit`
3. Sistem create deposit record dengan deadline 24 jam
4. Kirim notifikasi WhatsApp untuk upload bukti DP
5. Customer upload bukti transfer
6. Admin verifikasi:
   - **Approve**: Status → `deposit_confirmed`, kirim notifikasi
   - **Reject**: Status → `deposit_rejected`, kirim notifikasi + reason
7. Jika > 24 jam tidak bayar: Auto-expire → status `expired`

## 🎫 Voucher System

Admin bisa membuat voucher dengan:
- **Type**: Nominal atau Percentage
- **Minimal transaksi**: Syarat penggunaan
- **Periode aktif**: Valid from - Valid until
- **Usage limit**: Sekali pakai / berkali-kali / max usage
- **Landing page**: Tampil di landing page atau tidak

Contoh voucher berdasarkan transaksi:
- Transaksi 200rb → Voucher 15.000 - 950.000
- Transaksi 500rb → Voucher + doorprize

## 📸 Before-After Photos

- Upload hanya oleh admin melalui detail booking
- Customer hanya bisa lihat foto miliknya sendiri
- Owner tidak otomatis punya akses (bisa dikustomisasi)
- Foto disimpan di `storage/app/public/before-after/`

## 🔔 WhatsApp Notifications

### Template Notifikasi

**Konfirmasi Booking:**
```
*KONFIRMASI BOOKING* 🎉

Halo [Nama],

Booking Anda telah dikonfirmasi!

*Detail Booking:*
📋 Kode: [BOOKING_CODE]
💆 Treatment: [TREATMENT]
👨‍⚕️ Dokter: [DOCTOR]
📅 Tanggal: [DATE]
🕐 Jam: [TIME]
💰 Total: Rp [PRICE]

Terima kasih! 😊
```

**Menunggu DP:**
```
*MENUNGGU PEMBAYARAN DP* 💳

Halo [Nama],

Booking Anda memerlukan DP sebesar:
💰 Rp 50.000

⏰ *Batas waktu:* [DEADLINE]
(24 jam dari sekarang)

Silakan transfer dan upload bukti pembayaran melalui website.

Terima kasih! 😊
```

**DP Disetujui:**
```
*DP DISETUJUI* ✅

Halo [Nama],

DP Anda telah diverifikasi dan disetujui!

Booking Anda terkonfirmasi untuk:
📅 [DATE]
🕐 [TIME]

Sampai jumpa! 👋
```

## 🛠 Development Commands

```bash
# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Create new migration
php artisan make:migration create_table_name

# Create new controller
php artisan make:controller ControllerName

# Create new model
php artisan make:model ModelName

# Run tests
php artisan test

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Optimize
php artisan optimize
```

## 📁 Project Structure

```
app/
├── Console/
│   └── Commands/          # Cron jobs (expire deposits, reminders)
├── Http/
│   ├── Controllers/
│   │   ├── Auth/         # Authentication controllers
│   │   ├── Admin/        # Admin panel controllers
│   │   └── Customer/     # Customer area controllers
│   └── Middleware/
│       └── CheckRole.php # Role-based access control
├── Models/               # Eloquent models
└── Services/            # Business logic
    ├── WhatsAppService.php
    ├── OtpService.php
    └── BookingService.php

database/
├── migrations/          # Database schema
└── seeders/            # Sample data

resources/
├── views/              # Blade templates (to be created)
└── js/                # Frontend JavaScript

routes/
├── web.php            # Web routes
└── console.php        # Scheduled tasks
```

## 🔐 Security Features

- Password hashing dengan bcrypt
- OTP expiration (10 menit)
- OTP attempt limiting (max 5x)
- Resend OTP cooldown (60 detik)
- CSRF protection
- Role-based access control
- Input validation & sanitization

## 📊 Database Schema

### Core Tables
- `users` - Customer, admin, owner accounts
- `otp_verifications` - OTP codes untuk authentication
- `treatments` - Master treatment dengan durasi & harga
- `doctors` - Master dokter
- `doctor_schedules` - Jadwal dokter per hari
- `bookings` - Booking records
- `deposits` - DP management dengan deadline
- `vouchers` - Promo & voucher management
- `voucher_usages` - Tracking penggunaan voucher
- `feedbacks` - Rating & review dari customer
- `before_after_photos` - Dokumentasi hasil treatment
- `no_show_notes` - Catatan no-show (admin only)
- `settings` - Konfigurasi sistem

## 🎨 Frontend (To Do)

Frontend views belum dibuat dalam implementasi ini. Anda perlu membuat:

### Landing Page
- `resources/views/landing/index.blade.php`
- `resources/views/landing/treatments.blade.php`
- `resources/views/landing/vouchers.blade.php`

### Authentication
- `resources/views/auth/register.blade.php`
- `resources/views/auth/login.blade.php`
- `resources/views/auth/forgot-password.blade.php`

### Customer Area
- `resources/views/customer/dashboard.blade.php`
- `resources/views/customer/booking/*.blade.php`
- `resources/views/customer/feedback/*.blade.php`

### Admin Panel
- `resources/views/admin/dashboard.blade.php`
- `resources/views/admin/treatments/*.blade.php`
- `resources/views/admin/doctors/*.blade.php`
- `resources/views/admin/bookings/*.blade.php`
- `resources/views/admin/deposits/*.blade.php`
- `resources/views/admin/vouchers/*.blade.php`
- `resources/views/admin/members/*.blade.php`
- `resources/views/admin/feedbacks/*.blade.php`

**Rekomendasi:**
- Gunakan Tailwind CSS (sudah ter-install)
- Tambahkan Alpine.js untuk interaktivitas
- Gunakan Laravel Livewire (opsional) untuk real-time features

## 📞 Support

Untuk pertanyaan atau masalah, silakan buat issue atau hubungi developer.

## 📝 License

Proprietary - Klinik Kecantikan Internal Use Only

---

**Built with Laravel 11 ❤️**
