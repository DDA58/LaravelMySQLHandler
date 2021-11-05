<?php

declare(strict_types=1);

namespace DDA58\LaravelMySQLHandler\Container;

use DDA58\LaravelMySQLHandler\Services\Builder\Interfaces\IBuilder;
use Illuminate\Database\Query\Builder as DbQueryBuilder;
use SplObjectStorage;

/**
 * @package DDA58\LaravelMySQLHandler
 */
interface IHandlerContainer
{
    public function getDbBuildersToOpenedHandlers(): SplObjectStorage;

    public function remove(DbQueryBuilder $dbBuilder): self;

    public function containHandlerName(string $name): bool;

    public function add(DbQueryBuilder $dbBuilder, IBuilder $handlerBuilder): self;

    public function getHandlerByName(string $handlerName): ?IBuilder;
}
