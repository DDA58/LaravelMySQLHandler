<?php

declare(strict_types=1);

namespace DDA58\LaravelMySQLHandler\Services\Manager;

use DDA58\LaravelMySQLHandler\Container\IHandlerContainer;

/**
 * @package DDA58\LaravelMySQLHandler
 */
interface IHandlerManager
{
    public function getRepository(): IHandlerContainer;

    public function registerHandlerMethodsForQueryBuilder(): void;

    public function registerHandlerMethodsForEloquentBuilder(): void;
}
