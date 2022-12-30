<?php


namespace AhmetAksoy\CustomLogger;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Monolog\Logger;
use AhmetAksoy\CustomLogger\Handler\EloquentHandler;

class CustomLoggerServiceProvider extends ServiceProvider
{

    public function register()
    {
        /**
         * Eloquent Log Driver
         */
        Log::extend('eloquent', function ($app, $config) {
            $handler = new EloquentHandler($config['model'], $config['level'] ?? 'debug', $config['bubble'] ?? true);
            return new Logger('local', [$handler]);
        });

        parent::register();
    }
}
