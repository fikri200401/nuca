<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Force HTTPS on production (fixes mixed-content issues on hosting)
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // If public/storage symlink is broken, serve files directly via Laravel
        // This registers a disk URL that works even without symlink support
        if (! is_link(public_path('storage')) && ! is_dir(public_path('storage'))) {
            Storage::disk('public')->url('');   // trigger disk boot
        }
        /**
         * @canDo('module', 'action')
         *   ... show button/element ...
         * @endCanDo
         *
         * Example: @canDo('bookings', 'add') ... @endCanDo
         */
        Blade::directive('canDo', function (string $expression) {
            return "<?php if (auth()->check() && auth()->user()->canDo({$expression})): ?>";
        });

        Blade::directive('endCanDo', function () {
            return '<?php endif; ?>';
        });

        /**
         * @cannotDo('module', 'action')
         *   ... show fallback ...
         * @endCannotDo
         */
        Blade::directive('cannotDo', function (string $expression) {
            return "<?php if (!auth()->check() || !auth()->user()->canDo({$expression})): ?>";
        });

        Blade::directive('endCannotDo', function () {
            return '<?php endif; ?>';
        });
    }
}
