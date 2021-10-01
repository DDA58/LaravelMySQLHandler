<?php

declare(strict_types=1);

namespace DDA58\MySQLHandlerForLaravelQueryBuilder\Services\Manager;

use DDA58\MySQLHandlerForLaravelQueryBuilder\Container\IHandlerContainer;

/**
 * Interface IHandlerManager
 * @package DDA58\MySQLHandlerForLaravelQueryBuilder
 */
interface IHandlerManager
{
    public function getRepository(): IHandlerContainer;

    public function registerHandlerMethodsForQueryBuilder(): void;

    public function registerHandlerMethodsForEloquentBuilder(): void;
}
