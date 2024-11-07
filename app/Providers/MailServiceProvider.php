<?php

namespace App\Providers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use App\Services\GmailService;
use App\Mail\CustomMailTransport;

class MailServiceProvider extends ServiceProvider
{
    public function register()
    {
        // GmailServiceを使ってメール送信
        $this->app->singleton(GmailService::class, function ($app) {
            return new GmailService();
        });
    }

    public function boot()
    {
        Mail::extend('gmail', function () {
            return new GmailService();
        });
    }
}
