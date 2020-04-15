<?php

namespace App\Providers;

use App\Services\Telegram\TelegramAPI;
use Illuminate\Support\ServiceProvider;

class TelegramAPIServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('TelegramAPI', TelegramAPI::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
