<?php

declare(strict_types=1);

namespace DDA58\LaravelMySQLHandler\Facades;

use DDA58\LaravelMySQLHandler\Container\IHandlerContainer;
use Illuminate\Support\Facades\Facade;

/**
 * @package DDA58\LaravelMySQLHandler
 * @method static IHandlerContainer getFacadeRoot()
 */
class HandlerContainer extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'mysql.handler.container';
    }
}
