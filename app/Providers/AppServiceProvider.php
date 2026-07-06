<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Mariuzzo\LaravelJsLocalization\Commands\LangJsCommand;
use Mariuzzo\LaravelJsLocalization\Generators\LangJsGenerator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
            $laravelMajorVersion = (int)$app::VERSION;
            $files = $app['files'];

            $langs = '';
            if ($laravelMajorVersion === 4) {
                $langs = $app['path.base'] . '/app/lang';
            }
            elseif ($laravelMajorVersion >= 5 && $laravelMajorVersion < 9) {
                $langs = $app['path.base'] . '/resources/lang';
            }
            elseif ($laravelMajorVersion >= 9) {
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
    public function boot()
    {
        app()->useLangPath(base_path('lang'));
        Paginator::useBootstrap();
        Schema::defaultStringLength(191);

        // Prevent database queries during artisan commands
        if (app()->runningInConsole()) {
            return;
        }

        try {
            // Fetch timezone from settings table
            $newTimezone = DB::table('settings')
                ->where('key', 'timezone')
                ->value('value');

            if (!$newTimezone) {
                $newTimezone = 'UTC';
            }

            config(['app.timezone' => $newTimezone]);
            date_default_timezone_set($newTimezone);

            // Set Carbon locale properly (language, NOT timezone)
            Carbon::setLocale(app()->getLocale());

        }
        catch (\Exception $e) {
            // Fallback values
            config(['app.timezone' => 'UTC']);
            date_default_timezone_set('UTC');
            Carbon::setLocale('en');
        }
    }
}