<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Mariuzzo\LaravelJsLocalization\Commands\LangJsCommand;
use Mariuzzo\LaravelJsLocalization\Generators\LangJsGenerator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\LiveWireController;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('localization.js', function ($app) {
            $app = $this->app;
            $laravelMajorVersion = (int) $app::VERSION;
            $files = $app['files'];

            $langs = '';
            if ($laravelMajorVersion === 4) {
                $langs = $app['path.base'] . '/app/lang';
            } elseif ($laravelMajorVersion >= 5 && $laravelMajorVersion < 9) {
                $langs = $app['path.base'] . '/resources/lang';
            } elseif ($laravelMajorVersion >= 9) {
                $langs = app()->langPath();
            }
            $messages = $app['config']->get('localization-js.messages');
            $generator = new LangJsGenerator($files, $langs, $messages);

            return new LangJsCommand($generator);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    // public function boot()
    // {
    //     app()->useLangPath(base_path('lang'));
    //     Paginator::useBootstrap();
    //     // LiveWireController::check();
    //     Schema::defaultStringLength(191);


    //     //setting up time
    //     $newTimezone = DB::table('settings')->where('key', 'timezone')->value('value');
    //     config(['app.timezone' => $newTimezone]);
    //     date_default_timezone_set($newTimezone);
    //     Carbon::setLocale($newTimezone);
    // }


    public function boot()
    {
        app()->useLangPath(base_path('lang'));
        Paginator::useBootstrap();
        LiveWireController::check();
        Schema::defaultStringLength(191);

        // Setting up timezone
        $newTimezone = DB::table('settings')->where('key', 'timezone')->value('value');

        // Default fallback if not set or invalid
        if (!$newTimezone) {
            $newTimezone = 'UTC';
        }

        // Set timezone (correct)
        config(['app.timezone' => $newTimezone]);
        date_default_timezone_set($newTimezone);

        // Set Carbon locale (use language like 'en' or 'ar', NOT timezone)
        Carbon::setLocale(app()->getLocale()); // or just 'en'
    }
}