<?php 
namespace Selkis\Elastix;
use Illuminate\Support\ServiceProvider;
 
class ElastixServiceProvider extends ServiceProvider {
 
   /**
     * Bootstrap the application services.
     *
     * @return void
    */
  public function boot()
  {
    $this->publishes([__DIR__.'/config/elastix.php' => config_path('elastix.php')]);
    $this->loadViewsFrom(__DIR__.'/views', 'elastix');
    $this->publishes([
        __DIR__.'/public' => public_path('/'),
    ], 'public');
  }
 
  /**
    * Register the application services.
    *
    * @return void
  */
  public function register()
  {
    include __DIR__.'/routes.php';
    $this->app->make('Selkis\Elastix\Controllers\ControllerTelephonePanel');
  }
 
}