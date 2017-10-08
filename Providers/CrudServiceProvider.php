<?php

namespace Modules\Crud\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Crud\ViewComposers\NavTabsComposer;

class CrudServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        //$this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerViewComposers();
        //$this->registerFactories();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('crud.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'crud'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/crud');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ]);

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/crud';
        }, \Config::get('view.paths')), [$sourcePath]), 'crud');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/crud');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'crud');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'crud');
        }
    }

    /**
     * Register an additional directory of factories.
     * @source https://github.com/sebastiaanluca/laravel-resource-flow/blob/develop/src/Modules/ModuleServiceProvider.php#L66
     */
    public function registerFactories()
    {
        if (! app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/Database/factories');
        }
    }

    /**
     *
     */
    public function registerViewComposers()
    {
        $map = [
            'crud::nav_tabs' => NavTabsComposer::class
        ];

        foreach ($map as $view => $composerClass) {
            view()->composer($view, $composerClass);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}