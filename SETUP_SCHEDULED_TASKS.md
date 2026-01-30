# Setup Scheduled Tasks - Sistem Reservasi Klinik

## Kenapa Perlu Scheduled Tasks?

Sistem ini membutuhkan task otomatis yang berjalan secara berkala untuk:

1. **Auto-expire Deposits** - Membatalkan booking yang belum bayar DP dalam 24 jam
2. **Send Booking Reminders** - Mengirim notifikasi WhatsApp reminder H-1 sebelum appointment

---

## 📋 Scheduled Tasks yang Tersedia

### 1. Expire Deposits (Setiap Menit)
```bash
php artisan deposits:expire
```

**Fungsi:**
- Cek semua deposit dengan status `pending`
- Jika sudah melewati `deadline_at` (24 jam)
- Update status deposit → `expired`
- Update status booking → `expired`
- Kirim notifikasi WhatsApp ke customer
- Release slot booking agar tersedia lagi

**Schedule:** Setiap 1 menit

---

### 2. Send Booking Reminders (Setiap Jam)
```bash
php artisan bookings:send-reminders
```

**Fungsi:**
- Cek semua booking untuk besok (H-1)
- Booking dengan status `auto_approved` atau `deposit_confirmed`
- Kirim notifikasi WhatsApp reminder
- Mark sebagai reminded agar tidak kirim lagi

**Schedule:** Setiap 1 jam (atau bisa disesuaikan)

---

## 🪟 Setup di Windows (Laragon/XAMPP)

### Cara 1: Manual Testing (Development)

Jalankan command manual untuk testing:

```bash
# Buka terminal di folder project
cd c:\laragon\www\reservasi

# Test expire deposits
php artisan deposits:expire

# Test send reminders
php artisan bookings:send-reminders

# Atau jalankan scheduler
php artisan schedule:run
```

### Cara 2: Task Scheduler Windows (Production)

1. **Buka Task Scheduler**
   - Tekan `Win + R`
   - Ketik `taskschd.msc`
   - Enter

2. **Create New Task**
   - Klik `Create Basic Task`
   - Name: `Laravel Scheduler - Reservasi`
   - Description: `Run Laravel scheduled tasks every minute`

3. **Trigger**
   - Pilih: `Daily`
   - Start: Hari ini
   - Recur every: `1` days
   - Centang: `Repeat task every: 1 minute`
   - For a duration of: `Indefinitely`

4. **Action**
   - Pilih: `Start a program`
   - Program/script: `C:\laragon\bin\php\php-8.2.0-Win32-vs16-x64\php.exe`
     (Sesuaikan dengan path PHP Laragon Anda)
   - Add arguments: `artisan schedule:run`
   - Start in: `c:\laragon\www\reservasi`

5. **Conditions**
   - Uncheck: `Start the task only if the computer is on AC power`

6. **Settings**
   - Check: `Run task as soon as possible after a scheduled start is missed`
   - Check: `If the task fails, restart every: 1 minute`

7. **Finish**
   - Klik `Finish`

### Cara 3: Batch Script (Alternatif)

Buat file `run-scheduler.bat` di folder project:

```batch
@echo off
cd /d c:\laragon\www\reservasi
c:\laragon\bin\php\php-8.2.0-Win32-vs16-x64\php.exe artisan schedule:run
```

Lalu setup Task Scheduler untuk menjalankan file `.bat` ini setiap 1 menit.

---

## 🐧 Setup di Linux/Mac

### Cara 1: Crontab

```bash
# Edit crontab
crontab -e

# Tambahkan baris ini:
* * * * * cd /path/to/reservasi && php artisan schedule:run >> /dev/null 2>&1
```

**Penjelasan:**
- `* * * * *` = Setiap menit
- `cd /path/to/reservasi` = Masuk ke folder project
- `php artisan schedule:run` = Jalankan Laravel scheduler
- `>> /dev/null 2>&1` = Redirect output (opsional)

### Cara 2: Systemd Service (Advanced)

1. **Buat service file**

```bash
sudo nano /etc/systemd/system/reservasi-scheduler.service
```

Isi dengan:

```ini
[Unit]
Description=Laravel Scheduler for Reservasi
After=network.target

[Service]
Type=oneshot
User=www-data
WorkingDirectory=/var/www/reservasi
ExecStart=/usr/bin/php artisan schedule:run

[Install]
WantedBy=multi-user.target
```

2. **Buat timer file**

```bash
sudo nano /etc/systemd/system/reservasi-scheduler.timer
```

Isi dengan:

```ini
[Unit]
Description=Run Laravel Scheduler every minute
Requires=reservasi-scheduler.service

[Timer]
OnBootSec=1min
OnUnitActiveSec=1min
Unit=reservasi-scheduler.service

[Install]
WantedBy=timers.target
```

3. **Enable dan start**

```bash
sudo systemctl enable reservasi-scheduler.timer
sudo systemctl start reservasi-scheduler.timer
sudo systemctl status reservasi-scheduler.timer
```

---

## 📝 Registrasi Scheduled Tasks di Laravel

Edit file `routes/console.php`:

```php
<?php

use Illuminate\Support\Facades\Schedule;

// Auto-expire deposits setiap 1 menit
Schedule::command('deposits:expire')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

// Send booking reminders setiap jam
Schedule::command('bookings:send-reminders')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground();

// Cleanup old OTP codes setiap hari jam 2 pagi
Schedule::command('otp:cleanup')
    ->dailyAt('02:00')
    ->withoutOverlapping();
```

---

## 🧪 Testing Scheduled Tasks

### 1. Manual Run

```bash
# Test single command
php artisan deposits:expire
php artisan bookings:send-reminders

# Test scheduler
php artisan schedule:run

# List scheduled tasks
php artisan schedule:list
```

### 2. Test dengan Data Dummy

```bash
# Seed database dengan data testing
php artisan db:seed

# Buat booking dengan deadline lewat (untuk test expire)
# Edit manual di database atau buat seeder khusus

# Run scheduler
php artisan schedule:run

# Cek log
tail -f storage/logs/laravel.log
```

### 3. Monitor Task Scheduler

**Windows:**
- Buka Task Scheduler
- Lihat history di tab `History`

**Linux:**
- Check cron log: `tail -f /var/log/syslog | grep CRON`
- Check Laravel log: `tail -f storage/logs/laravel.log`

---

## ⚠️ Troubleshooting

### Task tidak jalan

**Windows:**
1. Pastikan path PHP benar
2. Cek Task Scheduler history
3. Jalankan manual dulu untuk test
4. Pastikan permission folder `storage` writable

**Linux:**
1. Cek crontab: `crontab -l`
2. Cek cron service: `sudo systemctl status cron`
3. Cek permission: `sudo chown -R www-data:www-data storage`
4. Cek log: `tail -f /var/log/syslog`

### Notifikasi WhatsApp tidak terkirim

1. Cek API Key Fonnte di `.env`
2. Test manual kirim OTP dari register
3. Cek quota Fonnte di dashboard
4. Cek log Laravel: `storage/logs/laravel.log`

### Deposit tidak auto-expire

1. Pastikan scheduler jalan
2. Cek command: `php artisan deposits:expire`
3. Cek timezone di `config/app.php`
4. Cek field `deadline_at` di database

---

## 📊 Monitoring & Logs

### Laravel Log

```bash
# Realtime log
tail -f storage/logs/laravel.log

# Filter error only
tail -f storage/logs/laravel.log | grep ERROR

# Clear old logs
> storage/logs/laravel.log
```

### Custom Logging di Commands

Edit command files untuk tambah logging:

```php
use Illuminate\Support\Facades\Log;

// Di dalam handle()
Log::info('Deposits expire command started');
Log::info('Expired deposits count: ' . $expiredCount);
Log::error('Failed to expire deposit: ' . $e->getMessage());
```

---

## 🚀 Best Practices

1. **Testing Dulu**
   - Jalankan manual sebelum setup cron/scheduler
   - Test dengan data dummy

2. **Monitoring**
   - Setup log monitoring
   - Check task history berkala

3. **Error Handling**
   - Tambahkan try-catch di commands
   - Log semua error untuk debugging

4. **Notification**
   - Setup email notification untuk critical errors
   - Monitor quota API WhatsApp

5. **Performance**
   - Use `withoutOverlapping()` untuk prevent duplicate run
   - Use `runInBackground()` untuk command berat

---

## ✅ Checklist Setup

- [ ] Install Laragon/XAMPP dan pastikan PHP tersedia
- [ ] Setup database dan jalankan migrations
- [ ] Test command manual: `php artisan deposits:expire`
- [ ] Test command manual: `php artisan bookings:send-reminders`
- [ ] Setup Task Scheduler (Windows) atau Crontab (Linux)
- [ ] Verify task jalan dengan cek log
- [ ] Monitor 24 jam pertama untuk pastikan stabil
- [ ] Setup monitoring & alerting

---

**Selamat! Scheduled tasks sudah siap untuk production! 🎉**
