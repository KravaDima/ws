<?php

declare(strict_types=1);

namespace WS\Providers;

use Illuminate\Support\ServiceProvider;
use WS\Client\Client;
use WS\Contracts\ReportParserContract;
use WS\WSReportParser;

class WSReportServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'ws');

        $this->app->bind(Client::class,
            function () {
                return new Client(config('ws.report_url'), config('ws.client_secret'));
            }
        );

        $this->app->bind(ReportParserContract::class, WSReportParser::class);
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('connection.php'),
        ], 'config');
    }
}
