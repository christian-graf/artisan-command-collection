<?php

namespace Fox\Artisan;

use Illuminate\Support\ServiceProvider;
use Fox\Artisan\Console\Commands\ClearCaches;
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
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
               ClearCaches::class,
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
