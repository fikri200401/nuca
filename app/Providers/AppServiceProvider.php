<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
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
