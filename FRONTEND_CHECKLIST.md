# Frontend Implementation Checklist

## ✅ Backend Status
- [x] Database migrations
- [x] Models & relationships
- [x] Controllers (Auth, Customer, Admin)
- [x] Services (WhatsApp, OTP, Booking)
- [x] Routes
- [x] Middleware
- [x] Seeders
- [x] Scheduled tasks

## 📝 Frontend To-Do

### 1. Landing Page (Public)

#### `resources/views/landing/index.blade.php`
- [ ] Header dengan logo & menu
- [ ] Hero section dengan CTA "Booking Sekarang"
- [ ] Section popular treatments (loop dari database)
- [ ] Section active vouchers bulan ini
- [ ] Info klinik (alamat, jam operasional)
- [ ] WhatsApp button (floating)
- [ ] Footer

#### `resources/views/landing/treatments.blade.php`
- [ ] List semua treatments
- [ ] Filter & search
- [ ] Card treatment dengan info: nama, harga, durasi, rating
- [ ] Link ke detail

#### `resources/views/landing/treatment-detail.blade.php`
- [ ] Detail treatment lengkap
- [ ] Daftar feedback/rating
- [ ] Button "Booking Treatment Ini"

---

### 2. Authentication Pages

#### `resources/views/auth/register.blade.php`
**Multi-step form:**
- [ ] Step 1: Input nomor WhatsApp
  - [ ] Button "Kirim OTP"
  - [ ] Cooldown timer (60 detik)
- [ ] Step 2: Verifikasi OTP
  - [ ] Input 6 digit OTP
  - [ ] Button "Kirim Ulang OTP"
  - [ ] Countdown expire (10 menit)
- [ ] Step 3: Lengkapi data
  - [ ] Nama, password, confirm password
  - [ ] (Opsional) Email, tanggal lahir, gender, alamat
  - [ ] Button "Daftar"

**JavaScript/AJAX:**
```javascript
// Send OTP
fetch('/register/send-otp', {
    method: 'POST',
    body: JSON.stringify({whatsapp_number: phone})
})

// Verify OTP
fetch('/register/verify-otp', {
    method: 'POST',
    body: JSON.stringify({whatsapp_number: phone, otp_code: otp})
})

// Complete registration
fetch('/register', {method: 'POST', body: formData})
```

#### `resources/views/auth/login.blade.php`
- [ ] Input identifier (WA/Username/Member Number)
- [ ] Input password
- [ ] Checkbox "Remember me"
- [ ] Link "Lupa password?"
- [ ] Link "Belum punya akun? Daftar"

#### `resources/views/auth/forgot-password.blade.php`
**Similar to register flow:**
- [ ] Step 1: Input identifier
- [ ] Step 2: Verifikasi OTP
- [ ] Step 3: Reset password baru

---

### 3. Customer Dashboard

#### `resources/views/customer/dashboard.blade.php`
- [ ] Welcome message dengan nama user
- [ ] Stats cards:
  - Total bookings
  - Completed bookings
  - Pending feedback
- [ ] Section "Upcoming Bookings"
  - [ ] List 5 booking mendatang
  - [ ] Link "Lihat Detail"
- [ ] Section "Past Bookings"
  - [ ] List 5 booking selesai
  - [ ] Button "Beri Feedback" jika belum
- [ ] Quick actions:
  - [ ] Button "Buat Booking Baru"

#### `resources/views/customer/booking/create.blade.php`
**Booking Form (Multi-step):**

**Step 1: Pilih Treatment**
- [ ] Dropdown atau card selection treatments
- [ ] Tampilkan: nama, durasi, harga
- [ ] Button "Next"

**Step 2: Pilih Tanggal & Waktu**
- [ ] Date picker (min: today)
- [ ] AJAX load available slots berdasarkan treatment
- [ ] Tampilkan slot dalam grid/list
- [ ] Highlight slot yang tidak available
- [ ] Button "Next"

**Step 3: Pilih Dokter**
- [ ] AJAX load available doctors untuk slot terpilih
- [ ] Card untuk setiap dokter (foto, nama, spesialisasi)
- [ ] Radio button untuk pilih
- [ ] Button "Next"

**Step 4: Review & Voucher**
- [ ] Summary booking (treatment, tanggal, waktu, dokter, harga)
- [ ] Input voucher code (optional)
- [ ] Button "Apply Voucher"
- [ ] Tampilkan diskon member (jika ada)
- [ ] Tampilkan total & final price
- [ ] Text area untuk notes
- [ ] Button "Konfirmasi Booking"

**JavaScript:**
```javascript
// Get available slots
$('#booking_date').change(function() {
    fetch('/customer/bookings/available-slots', {
        method: 'POST',
        body: {treatment_id, date}
    }).then(slots => renderSlots(slots))
})

// Get available doctors
$('.time-slot').click(function() {
    fetch('/customer/bookings/available-doctors', {
        method: 'POST',
        body: {treatment_id, date, time}
    }).then(doctors => renderDoctors(doctors))
})
```

#### `resources/views/customer/booking/index.blade.php`
- [ ] Filter (status, date range)
- [ ] Table/List bookings
  - Kode booking
  - Treatment
  - Dokter
  - Tanggal & Jam
  - Status (dengan badge warna)
  - Actions
- [ ] Pagination

#### `resources/views/customer/booking/show.blade.php`
**Detail Booking:**
- [ ] Info lengkap booking
- [ ] Status badge
- [ ] Info treatment & dokter
- [ ] Harga & diskon

**Jika status = waiting_deposit:**
- [ ] Alert: "Booking memerlukan DP"
- [ ] Deadline countdown
- [ ] Form upload bukti transfer
  - [ ] File input (image)
  - [ ] Button "Upload Bukti DP"

**Jika status = completed:**
- [ ] Button "Lihat Foto Before-After" (jika ada)
- [ ] Button "Beri Feedback" (jika belum)

**Jika ada before-after photos:**
- [ ] Section "Hasil Treatment"
- [ ] Image gallery (before & after side-by-side)

#### `resources/views/customer/feedback/create.blade.php`
- [ ] Info booking
- [ ] Star rating (1-5)
- [ ] Text area komentar
- [ ] Button "Kirim Feedback"

---

### 4. Admin Panel

#### Layout: `resources/views/admin/layouts/app.blade.php`
- [ ] Sidebar navigation
  - Dashboard
  - Treatments
  - Doctors
  - Bookings
  - Deposits
  - Vouchers
  - Members
  - Feedbacks
- [ ] Top navbar
  - User info
  - Logout button
- [ ] Main content area

#### `resources/views/admin/dashboard.blade.php`
- [ ] Stats cards (4 cards):
  - Total bookings today
  - Pending deposits
  - Expired deposits (alert jika ada)
  - Active vouchers
- [ ] Table "Upcoming Bookings" (10 rows)
- [ ] Table "Pending Deposits" (10 rows)
  - [ ] Quick approve/reject buttons

#### `resources/views/admin/treatments/index.blade.php`
- [ ] Button "Tambah Treatment"
- [ ] Table treatments
  - Nama, Durasi, Harga, Status, Popular
  - Actions: Edit, Toggle Status, Delete
- [ ] Pagination

#### `resources/views/admin/treatments/create.blade.php`
#### `resources/views/admin/treatments/edit.blade.php`
- [ ] Form fields:
  - Nama
  - Deskripsi
  - Durasi (menit)
  - Harga
  - Checkbox "Popular" (tampil di landing)
  - Checkbox "Active"

#### `resources/views/admin/doctors/index.blade.php`
- [ ] Button "Tambah Dokter"
- [ ] Table doctors
  - Foto, Nama, Spesialisasi, Status
  - Actions: Edit, Schedules, Toggle Status, Delete

#### `resources/views/admin/doctors/create.blade.php`
#### `resources/views/admin/doctors/edit.blade.php`
- [ ] Form fields:
  - Nama
  - Spesialisasi
  - Phone, Email
  - Bio
  - Upload foto
  - Checkbox "Active"

#### `resources/views/admin/doctors/schedules.blade.php`
- [ ] Info dokter
- [ ] Button "Tambah Jadwal"
- [ ] Table jadwal
  - Hari, Jam Mulai, Jam Selesai, Status
  - Actions: Toggle Status, Delete
- [ ] Modal/Form tambah jadwal:
  - Dropdown hari
  - Time picker start & end

#### `resources/views/admin/bookings/index.blade.php`
- [ ] Filter:
  - Status dropdown
  - Date range picker
  - Search (kode booking / nama customer)
- [ ] Button "Input Booking Manual" (dari WA)
- [ ] Table bookings
  - Kode, Customer, Treatment, Dokter, Tanggal, Status
  - Actions: View Detail

#### `resources/views/admin/bookings/show.blade.php`
**Detail Booking:**
- [ ] Info lengkap (customer, treatment, doctor, harga)
- [ ] Status badge

**Actions:**
- [ ] Button "Reschedule" → Modal form
  - Date picker
  - Time picker
  - Dokter dropdown
- [ ] Button "Cancel" → Confirmation + notes
- [ ] Button "Complete" → Mark as completed
- [ ] Text area "Admin Notes" → Update

**Upload Before-After:**
- [ ] Form upload foto
  - File input "Before Photo"
  - File input "After Photo"
  - Text area "Notes"
  - Button "Upload"
- [ ] Preview jika sudah ada

**No-Show Notes (jika perlu):**
- [ ] Button "Tambah No-Show Note"
- [ ] List no-show notes untuk customer ini

#### `resources/views/admin/bookings/create.blade.php`
**Manual Booking Entry:**
- [ ] Dropdown/Search customer
- [ ] Dropdown treatment
- [ ] Date picker
- [ ] Time picker
- [ ] Dropdown dokter (available)
- [ ] Text area notes
- [ ] Button "Buat Booking"

#### `resources/views/admin/deposits/index.blade.php`
- [ ] Filter by status
- [ ] Table deposits
  - Booking Code, Customer, Amount, Deadline, Status
  - Actions: View Detail
- [ ] Highlight yang mendekati deadline

#### `resources/views/admin/deposits/show.blade.php`
- [ ] Info booking
- [ ] Info deposit (amount, deadline, status)
- [ ] Image preview bukti transfer
- [ ] Jika pending:
  - [ ] Button "Approve"
  - [ ] Button "Reject" → Modal input reason

#### `resources/views/admin/vouchers/index.blade.php`
- [ ] Button "Tambah Voucher"
- [ ] Table vouchers
  - Code, Nama, Type, Value, Valid Until, Usage
  - Actions: Edit, Toggle Status, View Usage, Delete

#### `resources/views/admin/vouchers/create.blade.php`
#### `resources/views/admin/vouchers/edit.blade.php`
- [ ] Form fields:
  - Code (unique)
  - Nama
  - Deskripsi
  - Type (Nominal/Percentage) → Radio
  - Value
  - Min Transaksi
  - Valid From & Until (date range)
  - Checkbox "Single Use"
  - Max Usage (number)
  - Checkbox "Show on Landing"
  - Checkbox "Active"

#### `resources/views/admin/vouchers/usage.blade.php`
- [ ] Info voucher
- [ ] Stats: Total usage
- [ ] Table usage history
  - User, Booking, Discount Amount, Date

#### `resources/views/admin/members/index.blade.php`
- [ ] Filter: All / Member / Non-Member
- [ ] Search
- [ ] Table users
  - Nama, WA, Member Number, Member Status, Discount
  - Actions: View Detail

#### `resources/views/admin/members/show.blade.php`
- [ ] User info
- [ ] Stats:
  - Total bookings
  - Completed
  - Cancelled
  - No-show count
- [ ] Actions:
  - [ ] Button "Activate Member" (jika belum)
  - [ ] Button "Deactivate Member" (jika sudah)
  - [ ] Form "Update Discount" → Number input
- [ ] Section "No-Show Notes"
  - [ ] List notes
  - [ ] Button "Tambah Note" → Form
- [ ] Section "Booking History"
  - [ ] Table bookings

#### `resources/views/admin/feedbacks/index.blade.php`
- [ ] Filter:
  - Rating (1-5)
  - Treatment
  - Doctor
- [ ] Stats cards:
  - Average rating
  - Total feedbacks
  - Rating distribution (chart/bars)
- [ ] Table feedbacks
  - User, Booking, Treatment, Doctor, Rating, Comment
  - Visibility toggle
  - Actions: View Detail, Delete

---

### 5. Components (Reusable)

#### `resources/views/components/alert.blade.php`
- Success, error, warning, info alerts

#### `resources/views/components/modal.blade.php`
- Reusable modal component

#### `resources/views/components/booking-status-badge.blade.php`
- Badge dengan warna sesuai status

#### `resources/views/components/rating-stars.blade.php`
- Display star rating

---

## 🎨 UI/UX Recommendations

### Colors
```css
Primary: #6366f1 (indigo)
Success: #10b981 (green)
Warning: #f59e0b (yellow)
Danger: #ef4444 (red)
Info: #3b82f6 (blue)
```

### Status Colors
```
auto_approved: green
waiting_deposit: yellow
deposit_confirmed: green
deposit_rejected: red
expired: gray
completed: blue
cancelled: red
```

### Icons (Heroicons / FontAwesome)
- Calendar: booking date
- Clock: time
- User: customer/doctor
- Check: approve
- X: reject/cancel
- Upload: upload file
- Star: rating

---

## 📱 Responsive Design
- Mobile-first approach
- Breakpoints: sm (640), md (768), lg (1024), xl (1280)
- Hamburger menu untuk mobile
- Collapsible sidebar di admin

---

## ⚡ JavaScript Features

### Required
- [ ] OTP timer countdown
- [ ] Resend OTP cooldown
- [ ] Date picker (Flatpickr/AirDatepicker)
- [ ] Time slot selection (AJAX)
- [ ] Image preview sebelum upload
- [ ] Form validation
- [ ] Confirmation modals (SweetAlert2)
- [ ] Loading spinners

### Optional
- [ ] Real-time notifications (Pusher/Echo)
- [ ] Chart.js untuk statistics
- [ ] DataTables untuk advanced tables
- [ ] Alpine.js untuk reactive components

---

## 📦 Assets to Install

```bash
# Frontend packages (optional)
npm install alpinejs
npm install @heroicons/vue
npm install sweetalert2
npm install flatpickr
npm install chart.js
```

---

## ✅ Testing Checklist

### Authentication
- [ ] Register dengan OTP
- [ ] Login dengan WA/username/member number
- [ ] Forgot password
- [ ] Logout

### Customer Flow
- [ ] Buat booking (hari yang sama)
- [ ] Buat booking (7 hari ke depan)
- [ ] Upload bukti DP
- [ ] Lihat riwayat booking
- [ ] Beri feedback
- [ ] Lihat foto before-after

### Admin Flow
- [ ] Approve/reject DP
- [ ] Input booking manual
- [ ] Reschedule booking
- [ ] Cancel booking
- [ ] Complete booking
- [ ] Upload before-after photos
- [ ] Manage treatments/doctors/vouchers
- [ ] Activate member
- [ ] Add no-show note

---

**Frontend implementation bisa dimulai dari Landing Page → Auth → Customer → Admin secara bertahap.**

**Good luck! 🚀**
