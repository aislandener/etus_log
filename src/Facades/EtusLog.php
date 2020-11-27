<?php

namespace Etus\EtusLog\Facades;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Etus\EtusLog\RegisterLog to(string $url)
 */
class EtusLog extends Facade {

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'EtusLog';
    }

}
