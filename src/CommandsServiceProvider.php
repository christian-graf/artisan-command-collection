<?php

namespace Fox\Artisan;

use Illuminate\Support\ServiceProvider;
use Fox\Artisan\Console\Commands\ClearCaches;
use Fox\Artisan\Console\Commands\CreateDatabase;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;

class CommandsServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     *
     * @var ApplicationContract
     */
    protected $app;

    /**
     * {@inheritdoc}
     */
    protected $defer = true;

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
