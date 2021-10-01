<?php

declare(strict_types=1);

namespace DDA58\MySQLHandlerForLaravelQueryBuilder\Facades;

use DDA58\MySQLHandlerForLaravelQueryBuilder\Container\IHandlerContainer;
use Illuminate\Support\Facades\Facade;

/**
 * class HandlerRepository
 * @package DDA58\MySQLHandlerForLaravelQueryBuilder
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
