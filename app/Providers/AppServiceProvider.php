<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Mail\BrevoTransport;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->app['mail.manager']->extend('brevo', function () {
            return new BrevoTransport(env('BREVO_API_KEY'));
        });
    }
}