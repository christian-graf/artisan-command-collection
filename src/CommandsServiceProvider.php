<?php

declare(strict_types=1);

namespace Fox\Artisan;

use Illuminate\Support\ServiceProvider;
use Fox\Artisan\Console\Commands\ClearCaches;
use Fox\Artisan\Console\Commands\CreateDatabase;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;

class CommandsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * {@inheritdoc}
     *
     * @var ApplicationContract
     */
    protected $app;

    /**
     * Register bindings in the container.
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ClearCaches::class,
                CreateDatabase::class,
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function provides()
    {
        return ['commands'];
    }
}
