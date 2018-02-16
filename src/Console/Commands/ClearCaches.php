<?php

namespace Fox\Artisan\Console\Commands;

use Illuminate\Console\Command;

class ClearCaches extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $description =
        'Clears multiple caches used by your application. e.G. config or view caches';

    /**
     * {@inheritdoc}
     */
    protected $signature =
        'fox:cache:clear 
        {cache?* : Type of the cache(s) to clear - available values are [app|config|route|view]}
        {--all : Clear all caches}';

    /**
     * @var array
     */
    private $availableCacheTypes = ['app', 'config', 'route', 'view'];

    /**
     * Clears all designated caches.
     * Available caches to clear:
     *  - app|application   ... laravel application cache
     *  - config            ... laravel configuration cache
     *  - route             ... laravel route list cache
     *  - view              ... laravel view cache.
     *
     * @return int
     */
    public function handle()
    {
        $cachesToClear = $this->argument('cache') ?? [];
        if ($this->option('all')) {
            $cachesToClear = $this->availableCacheTypes;
        }

        if (empty($cachesToClear)) {
            $this->warn('No cache type defined!');

            return 0;
        }

        foreach ($cachesToClear as $cache) {
            $cache = strtolower(trim($cache));
            switch ($cache) {
                case 'application':
                case 'app':
                    $this->comment('clear application cache');
                    $this->call('cache:clear');
                    break;
                case 'config':
                    $this->comment('clear config cache');
                    $this->call('config:clear');
                    break;
                case 'route':
                    $this->comment('clear route cache');
                    $this->call('route:clear');
                    break;
                case 'view':
                    $this->comment('clear view cache');
                    $this->call('view:clear');
                    break;
                default:
                    $this->error('unknown cache type "' . $cache . '"');

                    return 1;
                    break;
            }
        }

        return 0;
    }
}
