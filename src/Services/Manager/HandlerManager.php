<?php

declare(strict_types=1);

namespace DDA58\LaravelMySQLHandler\Services\Manager;

use DDA58\LaravelMySQLHandler\Container\IHandlerContainer;
use DDA58\LaravelMySQLHandler\Services\Builder\Interfaces\IEloquentBuilder;
use DDA58\LaravelMySQLHandler\Services\Builder\Interfaces\IBuilder;
use DDA58\LaravelMySQLHandler\Services\Builder\Interfaces\IQueryBuilder;
use Illuminate\Database\Eloquent\Builder as DbEloquentBuilder;
use Illuminate\Database\Query\Builder as DbQueryBuilder;
use Closure;

/**
 * @package DDA58\LaravelMySQLHandler
 */
class HandlerManager implements IHandlerManager
{
    private const QUERY_BUILDER_INTERFACE = IQueryBuilder::class;
    private const ELOQUENT_BUILDER_INTERFACE = IEloquentBuilder::class;

    private IHandlerContainer $repository;

    public function __construct(IHandlerContainer $repository)
    {
        $this->repository = $repository;
    }

    public function getRepository(): IHandlerContainer
    {
        return $this->repository;
    }

    /**
     * Add handler methods to QueryBuilder
     *
     * @return void
     */
    public function registerHandlerMethodsForQueryBuilder(): void
    {
        DbQueryBuilder::macro(
            'openHandler',
            $this->getOpenHandlerClosure(static::QUERY_BUILDER_INTERFACE)
        );

        $getFrom = static fn(DbQueryBuilder $builder): string => $builder->from;

        DbQueryBuilder::macro(
            'getHandler',
            $this->getGetHandlerClosure($getFrom)
        );

        DbQueryBuilder::macro(
            'closeHandler',
            $this->getCloseHandlerClosure($getFrom)
        );
    }

    public function registerHandlerMethodsForEloquentBuilder(): void
    {
        DbEloquentBuilder::macro(
            'openHandler',
            $this->getOpenHandlerClosure(static::ELOQUENT_BUILDER_INTERFACE)
        );

        $getFrom = static fn(DbEloquentBuilder $builder): string => $builder->getQuery()->from;

        DbEloquentBuilder::macro(
            'getHandler',
            $this->getGetHandlerClosure($getFrom)
        );

        DbEloquentBuilder::macro(
            'closeHandler',
            $this->getCloseHandlerClosure($getFrom)
        );
    }

    private function getOpenHandlerClosure(string $builderInterface): Closure
    {
        return function (string $alias = '') use ($builderInterface): IBuilder {
            /** @var $this DbQueryBuilder|DbEloquentBuilder */

            /** @var IBuilder $handlerBuilder */
            $handlerBuilder = resolve($builderInterface, ['dbBuilder' => $this]);

            return $handlerBuilder->open($alias);
        };
    }

    private function getGetHandlerClosure(Closure $getFrom): Closure
    {
        $handlerManager = $this;

        return function (string $handlerName = '') use ($handlerManager, $getFrom): IBuilder {
            /** @var $this DbQueryBuilder|DbEloquentBuilder */

            $handlerName = $handlerName ?: $getFrom($this);

            return $handlerManager->getRepository()->containHandlerName($handlerName) ?
                $handlerManager->getRepository()->getHandlerByName($handlerName) :
                $this->openHandler($handlerName);
        };
    }

    private function getCloseHandlerClosure(Closure $getFrom): Closure
    {
        $handlerManager = $this;

        return function (string $handlerName = '') use ($handlerManager, $getFrom): void {
            /** @var $this DbQueryBuilder|DbEloquentBuilder */

            $handlerName = $handlerName ?: $getFrom($this);

            if ($handlerManager->getRepository()->containHandlerName($handlerName)) {
                $handlerManager->getRepository()->getHandlerByName($handlerName)->close();
            }
        };
    }
}
