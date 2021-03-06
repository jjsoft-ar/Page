<?php namespace Modules\Page\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Modules\Page\Entities\Page;
use Modules\Page\Repositories\Cache\CachePageDecorator;
use Modules\Page\Repositories\Eloquent\EloquentPageRepository;

class PageServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBindings();
    }

    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../Config/config.php', 'asgard.page.config');
        $this->publishes([__DIR__ . '/../Config/config.php' => config_path('asgard.page.config' . '.php'), ], 'config');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

    private function registerBindings()
    {
        $this->app->bind(
            'Modules\Page\Repositories\PageRepository',
            function () {
                $repository = new EloquentPageRepository(new Page());

                if (! Config::get('app.cache')) {
                    return $repository;
                }

                return new CachePageDecorator($repository);
            }
        );
    }
}
