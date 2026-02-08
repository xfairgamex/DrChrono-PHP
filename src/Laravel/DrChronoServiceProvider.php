<?php

declare(strict_types=1);

namespace DrChrono\Laravel;

use DrChrono\Client\Config;
use DrChrono\DrChronoClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class DrChronoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/drchrono.php', 'drchrono');

        $this->app->singleton(DrChronoClient::class, function (): DrChronoClient {
            $config = new Config(config('drchrono', []));
            $config->setHttpClientType('laravel');
            $config->setLaravelPendingRequest($this->buildPendingRequest($config));

            return new DrChronoClient($config);
        });

        $this->app->alias(DrChronoClient::class, 'drchrono');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/drchrono.php' => config_path('drchrono.php'),
        ], 'drchrono-config');
    }

    private function buildPendingRequest(Config $config)
    {
        return Http::baseUrl($config->getBaseUri())
            ->timeout($config->getTimeout())
            ->connectTimeout($config->getConnectTimeout())
            ->acceptJson()
            ->withHeaders([
                'User-Agent' => $config->getUserAgent(),
            ]);
    }
}
