<?php

namespace Luna;

use HaydenPierce\ClassFinder\ClassFinder;
use Luna\Controllers\LunaResourceController;
use Luna\Controllers\LunaViewController;
use Luna\Middleware\AccessLuna;
use Luna\Middleware\AccessResource;
use Luna\Middleware\AccessView;
use Luna\Middleware\BootLuna;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\ClassLoader\ClassMapGenerator;

class LunaServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/Config/config.php' => config_path('luna.php'),
            __DIR__ . '/Luna' => app_path('Luna'),
            __DIR__ . '/Assets/view' => resource_path('views/luna'),
            //__DIR__ . '/Assets/dist' => public_path('luna'),
            __DIR__ . '/Assets/lang' => resource_path('lang'),
        ]);

        $this->registerRoutes();
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
            'luna' => [
                'luna.*',
                'login',
                'register',
                'logout'
            ],
        ]]);

        if (!$this->app->routesAreCached()) {
            $mids = config('luna.middleware', []);

            $this->app['router']->group([
                'as' => 'luna.',
                'prefix' => config('luna.route_prefix', 'luna'),
                'middleware' => array_merge($mids, [AccessLuna::class, BootLuna::class,])
            ], function () {
                $this->app['router']->get('app/{vue?}', LunaResourceController::class . '@index')->name('index')->where('vue', '[\/\w\.-]*');

                $this->app['router']->group([
                    'as' => 'resources.',
                    'prefix' => 'resources/{luna_resource}',
                    'middleware' => [AccessResource::class]
                ], function () {
                    $this->app['router']->any('type/{type}', LunaResourceController::class . '@typeRetrieve')->name('type-retrieve');
                    $this->app['router']->get('metric/{metric}', LunaResourceController::class . '@metric')->name('metric');

                    $this->app['router']->group(['as' => 'action.', 'prefix' => 'action/{action}'], function () {
                        $this->app['router']->get('init', LunaResourceController::class . '@initAction')->name('init');
                        $this->app['router']->post('handle', LunaResourceController::class . '@handleAction')->name('handle');
                    });

                    $this->app['router']->get('rearrange', LunaResourceController::class . '@rearrange')->name('rearrange');
                    $this->app['router']->post('rearrange', LunaResourceController::class . '@doRearrange');

                    $this->app['router']->get('paginate', LunaResourceController::class . '@paginate')->name('paginate');
                    $this->app['router']->post('create', LunaResourceController::class . '@create')->name('create');

                    $this->app['router']->group(['prefix' => '{luna_resource_id}'], function () {
                        $this->app['router']->get('/', LunaResourceController::class . '@details')->name('details');
                        $this->app['router']->get('edit', LunaResourceController::class . '@edit')->name('edit');
                        $this->app['router']->post('update', LunaResourceController::class . '@update')->name('update');
                        $this->app['router']->post('destroy', LunaResourceController::class . '@destroy')->name('destroy');

                        $this->app['router']->any('type/{type}', LunaResourceController::class . '@typeAction')->name('type-action');
                    });
                });

                $this->app['router']->group([
                    'as' => 'views.',
                    'prefix' => '{luna_view}',
                    'middleware' => [AccessView::class]
                ], function () {
                    $this->app['router']->get('/', LunaViewController::class . '@render')->name('render');
                });
            });
        }
    }
}
