<?php

declare(strict_types=1);

namespace DDA58\MySQLHandlerForLaravelQueryBuilder\Container;

use DDA58\MySQLHandlerForLaravelQueryBuilder\Services\Builder\Interfaces\IBuilder;
use Illuminate\Database\Query\Builder as DbQueryBuilder;
use SplObjectStorage;

/**
 * Interface IHandlerRepository
 * @package DDA58\MySQLHandlerForLaravelQueryBuilder
 */
interface IHandlerContainer
{
    public function getDbBuildersToOpenedHandlers(): SplObjectStorage;

    public function remove(DbQueryBuilder $dbBuilder): self;

    public function containHandlerName(string $name): bool;

    public function add(DbQueryBuilder $dbBuilder, IBuilder $handlerBuilder): self;

    public function getHandlerByName(string $handlerName): ?IBuilder;
}
