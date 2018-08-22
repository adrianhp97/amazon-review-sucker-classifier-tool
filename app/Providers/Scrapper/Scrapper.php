<?php

namespace AmazonReviewSuckerClassifierTool\Providers\Scrapper;

use Illuminate\Support\Facades\Facade;

class Scrapper extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'scrapper';
    }
}