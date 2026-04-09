<?php

namespace Authentica\LaravelAuthentica\Facades;

use Illuminate\Support\Facades\Facade;

class Authentica extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'authentica';
    }
}