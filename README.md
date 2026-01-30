# Reservasi — Nuca Beauty Skin Booking System

Laravel-based booking/reservation system for Nuca Beauty Skin.

This repository contains the backend Laravel application, frontend assets using Tailwind CSS and Vue 3, and services for handling bookings, deposits, and WhatsApp notifications.

## Features

- Customer booking flow (treatments, date/time, doctor selection)
- Deposit logic and booking statuses (auto-approved vs waiting deposit)
- Admin and customer booking management
- WhatsApp notification integration
- Tailwind CSS + Vue 3 frontend components

## Tech stack

- PHP (Laravel)
- MySQL / MariaDB
- Tailwind CSS
- Vue 3 (Composition API)
- Node.js + npm

## Prerequisites

- PHP 8.1+
- Composer
- Node.js and npm
- MySQL / MariaDB
- Git

On Windows, Laragon is recommended for a fast local setup.

## Quick setup (development)

1. Clone repository

```bat
git clone https://github.com/fikri200401/Reservasi.git
cd Reservasi
```

2. Copy environment file

```bat
copy .env.example .env
```

Edit `.env` and set DB credentials, mail settings, and any third-party keys (WhatsApp API credentials).

3. Install PHP dependencies

```bat
composer install
```

4. Generate application key

```bat
php artisan key:generate
```

5. Run migrations and seeders

```bat
php artisan migrate --seed
```

6. Install frontend dependencies and build assets

```bat
npm install
npm run dev   # use npm run build for production
```

7. Create storage symlink

```bat
php artisan storage:link
```

8. Start server

```bat
php artisan serve
```

Alternatively use Laragon to serve the project and manage the database.

## Running tests

```bat
php artisan test
```

## Deposit policy

- Bookings with `booking_date` less than 7 days from the current date are auto-approved (no deposit required).
- Bookings with `booking_date` greater than or equal to 7 days require a deposit and will show `WAITING_DEPOSIT` status until deposit is made.

To test the `WAITING_DEPOSIT` flow, create a booking where the appointment date is >= today + 7 days.

## Notes on theme and UI

The project uses a pink/magenta theme (primary: `#EC4899`) and gradients. Dropdowns and components are implemented as Blade components (see `resources/views/components/`). If you see dark borders around dropdowns, those are controlled via Tailwind classes (e.g., `ring-black`) and can be adjusted in the component files.

## Contributing

We welcome contributions! Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

Quick guidelines:
- Fork the repository and create a feature branch
- Follow PSR-12 coding standards
- Write tests for new features
- Keep the pink/magenta theme consistent
- Open a pull request with a clear description

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Troubleshooting

If you run into issues, please provide:

```bat
php -v
composer -V
node -v
npm -v
```

And any relevant Laravel log output from `storage/logs/laravel.log`.

---

**Built with ❤️ for Beauty Skin from fikr and ghz**

<img width="1146" height="4547" alt="Image" src="https://github.com/user-attachments/assets/a0ee7ade-f375-4f03-a42a-b64b07a33943" />
