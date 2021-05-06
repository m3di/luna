<?php

namespace Luna;

use HaydenPierce\ClassFinder\ClassFinder;
use Luna\Controllers\LunaController;
use Luna\Middleware\AccessLuna;
use Luna\Middleware\AccessResource;
use Luna\Middleware\BootLuna;
use Illuminate\Support\ServiceProvider;

class LunaServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/Config/config.php' => config_path('luna.php'),
            __DIR__ . '/Luna' => app_path('Luna'),
            __DIR__ . '/Assets/view' => resource_path('views/luna'),
            __DIR__ . '/Assets/dist' => public_path('luna'),
            __DIR__ . '/Assets/lang' => resource_path('lang'),
        ]);

        $this->registerRoutes();
        $this->registerResources();
        $this->registerTools();
        $this->registerMenu();
    }

    public function register()
    {
        $this->app->singleton('luna', function ($app) {
            return new Luna($app);
        });
    }

    private function registerRoutes()
    {
        config(['ziggy.groups' => [
            'luna' => ['luna.*'],
        ]]);

        if (!$this->app->routesAreCached()) {
            $this->app['router']->group([
                'as' => 'luna.',
                'prefix' => config('luna.route_prefix', 'luna'),
                'middleware' => [
                    'web',
                    'auth',
                    AccessLuna::class,
                    BootLuna::class,
                ],
            ], function () {
                $this->app['router']->get('app/{vue?}', LunaController::class . '@index')->name('index')->where('vue', '[\/\w\.-]*');

                $this->app['router']->group([
                    'as' => 'resources.',
                    'prefix' => 'resources',
                ], function () {
                    $this->app['router']->get('meta', LunaController::class . '@meta')->name('meta');

                    $this->app['router']->group([
                        'prefix' => '{luna_resource}',
                        'middleware' => [AccessResource::class]
                    ], function () {
                        $this->app['router']->get('paginate', LunaController::class . '@paginate')->name('paginate');
                        $this->app['router']->any('type/{type}', LunaController::class . '@typeRetrieve')->name('type-retrieve');
                        $this->app['router']->post('action/{action}', LunaController::class . '@action')->name('action');
                        $this->app['router']->get('metric/{metric}', LunaController::class . '@metric')->name('metric');

                        $this->app['router']->post('create', LunaController::class . '@create')->name('create');

                        $this->app['router']->group(['prefix' => '{luna_resource_id}'], function () {
                            $this->app['router']->get('/', LunaController::class . '@details')->name('details');
                            $this->app['router']->get('edit', LunaController::class . '@edit')->name('edit');
                            $this->app['router']->post('update', LunaController::class . '@update')->name('update');
                            $this->app['router']->post('destroy', LunaController::class . '@destroy')->name('destroy');

                            $this->app['router']->any('type/{type}', LunaController::class . '@typeAction')->name('type-action');
                        });
                    });
                });
            });
        }
    }

    private function registerResources()
    {
        $mode = config('luna.resources.mode', 'auto');

        if (!in_array($mode, ['auto', 'manual'])) {
            throw new \Exception("Luna resource register mode [{$mode}] is not valide.");
        }

        if ($mode == 'auto') {
            $path = config('luna.resources.auto', app_path('App\\Luna\\Resources\\'));
            $this->app['luna']->setResources(ClassFinder::getClassesInNamespace($path, ClassFinder::RECURSIVE_MODE));
        }


        if ($mode == 'manual') {
            $this->app['luna']->setResources(config('luna.resources.manual', []));
        }
    }

    private function registerTools()
    {
        // $this->app['luna']->setTools($this->tools());
    }

    private function registerMenu()
    {
        $this->app['luna']->setMenu(config('luna.menu', [
            \Luna\Menu\MenuItemAllResources::make('منابغ', 'fa fa-database'),
        ]));
    }
}
