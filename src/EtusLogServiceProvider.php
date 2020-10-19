<?php
 
namespace Etus\EtusLog;

use Illuminate\Support\ServiceProvider;

class EtusLogServiceProvider extends ServiceProvider{
    public function boot()
    {

        $configPath = __DIR__ . '/../config/etus_log.php';
        if (function_exists('config_path')) {
            $publishPath = config_path('etus_log.php');
        } else {
            $publishPath = base_path('config/etus_log.php');
        }
        $this->publishes([
            $configPath => $publishPath,
        ]);
    }

    public function register()
    {
        $configPath = __DIR__ . '/../config/etus_log.php';
        $this->mergeConfigFrom($configPath, 'etus_log');
    }
}
