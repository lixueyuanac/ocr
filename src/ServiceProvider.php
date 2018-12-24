<?php

namespace Xueyuan\Ocr;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

    class ServiceProvider extends LaravelServiceProvider
{
    protected $defer = true;


    public function boot()
    {
        if (function_exists('config_path')) {
            $publishPath = config_path('ocr.php');
        } else {
            $publishPath = base_path('config/ocr.php');
        }
        $this->publishes([
            __DIR__ . '/config/ocr.php' => $publishPath,
        ], 'config');
    }

    public function register()
    {
        $this->app->singleton(Ocr::class, function ($app) {
            return new Factory(Config('ocr.ocr'));
        });
    }

    public function provides()
    {
        return ['Xueyuan\\Ocr\\Ocr'];
    }
}