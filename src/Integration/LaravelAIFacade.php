<?php

namespace App\Integration;

use Illuminate\Support\Facades\Facade;

/**
 * Laravel facade for PHP and Laravel code classifier.
 */
class LaravelAIFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-ai';
    }
}
