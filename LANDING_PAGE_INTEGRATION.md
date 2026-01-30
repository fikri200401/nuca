# Landing Page Integration - Dokumentasi

## 📋 Perubahan yang Dilakukan

### 1. **File yang Diubah**
- ✅ `resources/views/landing/index.blade.php` - Landing page utama dengan desain baru
- ✅ `routes/web.php` - Route name untuk treatment detail

### 2. **File Backup**
- 📁 `resources/views/landing/index_backup.blade.php` - Backup landing page lama
- 📁 `resources/views/landing/index_old.blade.php` - Backup versi sebelumnya (jika ada)

---

## 🎨 Fitur Landing Page Baru

### **Desain & Tema**
- **Warna Utama**: Pink/Magenta (#ff4d88)
- **Font**: Inter (sans-serif) & Playfair Display (serif)
- **Framework CSS**: Tailwind CSS (CDN)
- **Icons**: Font Awesome 6.0

### **Sections yang Terintegrasi dengan Database**

#### 1️⃣ **Navigation Bar**
```blade
@auth
    <a href="{{ route('customer.dashboard') }}">Dashboard</a>
@else
    <a href="{{ route('login') }}">Masuk Akun</a>
@endauth
<a href="{{ route('customer.bookings.create') }}">Reservasi Sekarang</a>
```

#### 2️⃣ **Hero Section**
- Menggunakan data dari `$clinicInfo['name']`
- Link ke halaman booking

#### 3️⃣ **Perawatan Populer** (Database Driven)
```php
@forelse($popularTreatments as $treatment)
    <h3>{{ $treatment->name }}</h3>
    <p>{{ Str::limit($treatment->description, 80) }}</p>
    <p>{{ $treatment->formatted_price }}</p>
    <a href="{{ route('landing.treatment-detail', $treatment->id) }}">
        Lihat Selengkapnya
    </a>
@empty
    <p>Belum ada perawatan populer tersedia.</p>
@endforelse
```

**Data dari Controller**:
- `Treatment::active()->popular()->limit(6)->get()`
- Menampilkan nama, deskripsi (80 karakter), harga terformat
- Badge "Populer" jika `is_popular = true`

#### 4️⃣ **Promo Section** (Database Driven)
```php
@forelse($activeVouchers->take(3) as $voucher)
    <h4>{{ $voucher->name }}</h4>
    <p>{{ Str::limit($voucher->description, 50) }}</p>
    <p>Berakhir {{ $voucher->valid_until->format('d M Y') }}</p>
@empty
    {{-- Fallback promo statis --}}
@endforelse
```

**Data dari Controller**:
- `Voucher::active()->forLanding()->get()`
- Menampilkan 3 voucher teratas
- Fallback ke promo statis jika tidak ada data

#### 5️⃣ **Keunggulan Klinik**
6 keunggulan dengan icon Font Awesome:
- Tenaga Medis Profesional
- Teknologi Modern
- Pelayanan Terbaik
- Treatment Wajah Holistik
- Harga Terjangkau
- 15 Cabang di Kota Besar

#### 6️⃣ **Alur Booking**
- Step-by-step visual guide
- Link ke halaman registrasi dan booking
- Interactive card di sidebar

#### 7️⃣ **Status Pemesanan**
Form untuk cek booking dengan kode:
```blade
<form action="#" method="POST">
    @csrf
    <input name="booking_code" placeholder="Misal: NB-2023001">
    <div>{{ $appointment_date ?? '--/--/----' }}</div>
</form>
```

#### 8️⃣ **Testimonial**
3 testimoni klien dengan foto dan rating bintang 5

#### 9️⃣ **Artikel Kecantikan**
3 artikel dengan kategori (Kulit, Rambut, Tips)

#### 🔟 **FAQ Section**
- Accordion style untuk pertanyaan
- WhatsApp button terintegrasi dengan nomor dari database

#### 1️⃣1️⃣ **Footer** (Database Driven)
```blade
<h3>{{ $clinicInfo['name'] ?? 'Nuca Beauty Skin' }}</h3>
<p>{{ $clinicInfo['address'] ?? 'Jl. Kesehatan...' }}</p>
<p>{{ $clinicInfo['phone'] ?? '+62 812...' }}</p>

@if(!empty($clinicInfo['whatsapp']))
    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $clinicInfo['whatsapp']) }}">
        Chat WhatsApp
    </a>
@endif
```

---

## 🔌 Integrasi dengan Backend

### **Controller: LandingController.php**

```php
public function index()
{
    // Get popular treatments (max 6)
    $popularTreatments = Treatment::active()
        ->popular()
        ->limit(6)
        ->get();

    // Get active vouchers for landing page
    $activeVouchers = Voucher::active()
        ->forLanding()
        ->get();

    // Get clinic info from settings
    $clinicInfo = [
        'name' => Setting::get('clinic_name', 'Klinik Kecantikan'),
        'address' => Setting::get('clinic_address', ''),
        'phone' => Setting::get('clinic_phone', ''),
        'whatsapp' => Setting::get('clinic_whatsapp', ''),
        'operating_hours' => Setting::get('operating_hours', 'Senin - Sabtu: 09:00 - 18:00'),
        'about' => Setting::get('about', ''),
    ];

    return view('landing.index', compact('popularTreatments', 'activeVouchers', 'clinicInfo'));
}
```

### **Models yang Digunakan**

#### Treatment.php
- `scopeActive()` - Filter treatment yang aktif
- `scopePopular()` - Filter treatment populer
- `getFormattedPriceAttribute()` - Format harga Rupiah
- `getAverageRatingAttribute()` - Rating rata-rata

#### Voucher.php
- `scopeActive()` - Filter voucher aktif & valid
- `scopeForLanding()` - Filter voucher untuk ditampilkan di landing
- `valid_until` - Tanggal berakhir (Carbon instance)

#### Setting.php
- `get($key, $default)` - Ambil setting dari database

---

## 🎯 Routes yang Digunakan

```php
// Landing Page
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/treatments/{id}', [LandingController::class, 'treatmentDetail'])
    ->name('landing.treatment-detail');

// Customer
Route::get('/register', ...)->name('register');
Route::get('/login', ...)->name('login');
Route::get('/customer/dashboard', ...)->name('customer.dashboard');
Route::get('/customer/bookings/create', ...)->name('customer.bookings.create');
```

---

## 📊 Database Requirements

### **Tabel: treatments**
```sql
- id (bigint)
- name (varchar)
- description (text)
- price (decimal)
- duration_minutes (int)
- is_active (boolean) ✓
- is_popular (boolean) ✓
```

### **Tabel: vouchers**
```sql
- id (bigint)
- code (varchar)
- name (varchar)
- description (text)
- type (enum: percentage/fixed)
- value (decimal)
- valid_from (date)
- valid_until (date) ✓
- is_active (boolean) ✓
- show_on_landing (boolean) ✓
```

### **Tabel: settings**
```sql
- key (varchar) - PRIMARY
- value (text)
- type (varchar)
```

**Settings yang Digunakan**:
- `clinic_name` - Nama klinik
- `clinic_address` - Alamat lengkap
- `clinic_phone` - Nomor telepon
- `clinic_whatsapp` - Nomor WhatsApp
- `operating_hours` - Jam operasional
- `about` - Tentang klinik

---

## ✅ Testing Checklist

### **Tampilan**
- [ ] Hero section menampilkan nama klinik dari database
- [ ] Perawatan populer muncul maksimal 6 item
- [ ] Harga treatment terformat dengan benar (Rp X.XXX)
- [ ] Voucher promo maksimal 3 item
- [ ] Tanggal voucher berakhir terformat (dd MMM YYYY)
- [ ] Footer menampilkan info klinik dari database

### **Navigasi**
- [ ] Tombol "Masuk Akun" redirect ke /login
- [ ] Tombol "Reservasi Sekarang" redirect ke booking
- [ ] Tombol "Lihat Selengkapnya" treatment redirect ke detail
- [ ] Link "Chat WhatsApp" generate URL wa.me dengan nomor yang benar

### **Responsiveness**
- [ ] Mobile view (< 768px)
- [ ] Tablet view (768px - 1024px)
- [ ] Desktop view (> 1024px)

### **Fallback Data**
- [ ] Jika tidak ada treatment populer, tampilkan pesan
- [ ] Jika tidak ada voucher, tampilkan promo statis
- [ ] Jika clinic info kosong, tampilkan default value

---

## 🚀 Cara Testing

1. **Akses Landing Page**
   ```
   http://127.0.0.1:8000
   ```

2. **Cek Data Treatment**
   - Pastikan ada treatment dengan `is_popular = 1` dan `is_active = 1`
   - Minimal 3-6 treatment untuk tampilan optimal

3. **Cek Data Voucher**
   - Pastikan ada voucher dengan `show_on_landing = 1` dan `is_active = 1`
   - `valid_from` <= hari ini <= `valid_until`

4. **Cek Settings**
   - Buka Admin > Konfigurasi
   - Isi semua setting: clinic_name, clinic_address, clinic_phone, clinic_whatsapp

---

## 🎨 Customization Guide

### **Ubah Warna Tema**
Edit bagian `tailwind.config` di `<script>`:
```javascript
colors: {
    brand: {
        light: '#ff8fa3',
        DEFAULT: '#ff4d88',  // Warna utama
        dark: '#e03e73',
    }
}
```

### **Ubah Font**
Edit bagian `<link>` Google Fonts:
```html
<link href="https://fonts.googleapis.com/css2?family=Inter...&family=Playfair+Display..." rel="stylesheet">
```

### **Tambah Section Baru**
Tambahkan section sebelum footer:
```blade
<section class="py-16 bg-white">
    <div class="container mx-auto px-6">
        {{-- Konten section baru --}}
    </div>
</section>
```

---

## 🐛 Troubleshooting

### **Treatment tidak muncul**
```php
// Cek di database
SELECT * FROM treatments WHERE is_active = 1 AND is_popular = 1;

// Jika kosong, tambahkan data
UPDATE treatments SET is_popular = 1 WHERE id IN (1,2,3,4,5,6);
```

### **Voucher tidak muncul**
```php
// Cek di database
SELECT * FROM vouchers 
WHERE is_active = 1 
AND show_on_landing = 1 
AND valid_from <= CURDATE() 
AND valid_until >= CURDATE();

// Jika kosong, tambahkan data
UPDATE vouchers SET show_on_landing = 1 WHERE id IN (1,2,3);
```

### **Clinic info tidak muncul**
```php
// Cek settings
SELECT * FROM settings WHERE `key` IN ('clinic_name', 'clinic_phone', 'clinic_whatsapp');

// Jika kosong, seed data
php artisan db:seed --class=SettingSeeder
```

---

## 📝 Notes

- Landing page menggunakan **Tailwind CSS CDN** untuk kemudahan development
- Untuk production, disarankan compile Tailwind dengan Laravel Mix/Vite
- Gambar treatment masih menggunakan placeholder Unsplash
- Upload gambar treatment melalui Admin Panel untuk gambar asli

---

**Dibuat pada**: {{ date('d F Y') }}
**Versi**: 2.0 (Integrated with Database)
