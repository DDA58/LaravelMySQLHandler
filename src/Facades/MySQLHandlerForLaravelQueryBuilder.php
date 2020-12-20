<?php

namespace DDA58\MySQLHandlerForLaravelQueryBuilder\Facades;

use Illuminate\Support\Facades\Facade;

/**
* class MySQLHandlerForLaravelQueryBuilder
* @package DDA58\MySQLHandlerForLaravelQueryBuilder
*/
class MySQLHandlerForLaravelQueryBuilder extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'mysqlhandlerforlaravelquerybuilder';
    }
}
