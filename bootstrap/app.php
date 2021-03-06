<?php

require_once __DIR__.'/../vendor/autoload.php';

 Dotenv::load(__DIR__.'/../');


/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    realpath(__DIR__ . '/../')
);

$app->withFacades();

// Register facades aliases

if (!class_exists('Config'))
    class_alias(\Illuminate\Support\Facades\Config::class, 'Config');

if (!class_exists('JWTAuth'))
    class_alias(\Tymon\JWTAuth\Facades\JWTAuth::class, 'JWTAuth');

if (!class_exists('JWTFactory'))
    class_alias(\Tymon\JWTAuth\Facades\JWTFactory::class, 'JWTFactory');

if (!class_exists('OAuth'))
    class_alias(\Artdarek\OAuth\Facade\OAuth::class, 'OAuth');

// Load configuration files
$app->configure('constants');
$app->configure('cors');
$app->configure('auth');
$app->configure('jwt');


// Enable eloquent
$app->withEloquent();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

$app->middleware([
    Barryvdh\Cors\HandleCors::class,
    App\Http\Middleware\FormattingRequestMiddleware::class,
]);

$app->routeMiddleware([
    'jwt.auth'    => \App\Http\Middleware\GetUserFromToken::class,
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);

// Third party service provider
$app->register(\Barryvdh\Cors\LumenServiceProvider::class);
$app->register(\Tymon\JWTAuth\Providers\JWTAuthServiceProvider::class);
$app->register(\Pvm\ArtisanBeans\ArtisanBeansServiceProvider::class);
$app->register(Vluzrmos\Tinker\TinkerServiceProvider::class);
$app->register(Bugsnag\BugsnagLaravel\BugsnagServiceProvider::class);

// enable query log
if(env('APP_DEBUG') AND env('APP_ENV') != 'testing')
    //DB::enableQueryLog();

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/


$app->group(['namespace' => 'App\Http\Controllers'], function ($app) {
    require __DIR__.'/../app/Http/routes.php';
});

return $app;
