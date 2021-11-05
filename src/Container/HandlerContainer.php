<?php

declare(strict_types=1);

namespace DDA58\LaravelMySQLHandler\Container;

use DDA58\LaravelMySQLHandler\Exceptions\HandlerBuilderNotFound;
use DDA58\LaravelMySQLHandler\Services\Builder\Interfaces\IBuilder;
use Illuminate\Database\Query\Builder as DbQueryBuilder;
use SplObjectStorage;

/**
 * @package DDA58\LaravelMySQLHandler
 */
class HandlerContainer implements IHandlerContainer
{
    private static SplObjectStorage $dbBuildersToOpenedHandlers;

    public function __construct(SplObjectStorage $storage)
    {
        static::$dbBuildersToOpenedHandlers = $storage;
    }

    public function getDbBuildersToOpenedHandlers(): SplObjectStorage
    {
        return static::$dbBuildersToOpenedHandlers;
    }

    public function remove(DbQueryBuilder $dbBuilder): self
    {
        static::$dbBuildersToOpenedHandlers->detach($dbBuilder);

        return $this;
    }

    public function containHandlerName(string $name): bool
    {
        while (static::$dbBuildersToOpenedHandlers->valid()) {
            if ($name === static::$dbBuildersToOpenedHandlers->getInfo()->getHandlerName()) {
                return true;
            }
            static::$dbBuildersToOpenedHandlers->next();
        }

        return false;
    }

    public function add(DbQueryBuilder $dbBuilder, IBuilder $handlerBuilder): self
    {
        static::$dbBuildersToOpenedHandlers->attach($dbBuilder, $handlerBuilder);

        return $this;
    }

    /**
     * @param string $handlerName
     * @return IBuilder
     * @throws HandlerBuilderNotFound
     */
    public function getHandlerByName(string $handlerName): IBuilder
    {
        while (static::$dbBuildersToOpenedHandlers->valid()) {
            if ($handlerName === static::$dbBuildersToOpenedHandlers->getInfo()->getHandlerName()) {
                return static::$dbBuildersToOpenedHandlers->getInfo();
            }
            static::$dbBuildersToOpenedHandlers->next();
        }

        throw new HandlerBuilderNotFound($handlerName);
    }
}
