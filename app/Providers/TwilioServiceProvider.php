<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Twilio\Rest\Client;

final class TwilioServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Client::class, fn () => new Client(
            config('services.twilio.sid'), config('services.twilio.token')
        ));
    }
}
