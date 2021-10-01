<?php

declare(strict_types=1);

namespace DDA58\MySQLHandlerForLaravelQueryBuilder\Facades;

use DDA58\MySQLHandlerForLaravelQueryBuilder\Services\Manager\IHandlerManager;
use Illuminate\Support\Facades\Facade;

/**
 * class HandlerManager
 * @package DDA58\MySQLHandlerForLaravelQueryBuilder
 * @method static IHandlerManager getFacadeRoot()
 */
class HandlerManager extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'mysql.handler.manager';
    }
}
