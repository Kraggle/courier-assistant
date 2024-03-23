<?php

namespace App\Providers;

use Laravel\Cashier\Cashier;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register(): void {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        Cashier::calculateTaxes();

        Blade::extend(function ($value) {
            return str_replace('@void', 'href="javascript:void(0)"', $value);
        });

        Blade::extend(function ($value) {
            return preg_replace('/\@log\((.+)\)/', '<?php K::log(${1}); ?>', $value);
        });

        Blade::extend(function ($value) {
            return preg_replace('/\@define\((.+)\)/', '<?php ${1}; ?>', $value);
        });

        Blade::extend(function ($value) {
            return preg_replace('/\@icon\((.+?)\)/', '<i class="<?php echo ${1}; ?>" aria-hidden="true"></i>', $value);
        });
    }
}
