<?php
 
namespace Etus\EtusLog;

use Illuminate\Support\ServiceProvider;

class EtusLogServiceProvider extends ServiceProvider{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/etus_log.php' => app()->basePath('config/etus_log.php'),
        ]);
    }

    public function register()
    {

    }
}