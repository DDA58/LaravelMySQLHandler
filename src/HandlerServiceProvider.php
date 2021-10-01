<?php

declare(strict_types=1);

namespace DDA58\MySQLHandlerForLaravelQueryBuilder;

use DDA58\MySQLHandlerForLaravelQueryBuilder\Container\HandlerContainer;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Container\IHandlerContainer;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Services\Builder\{EloquentBuilder,
    Interfaces\IEloquentBuilder,
    Interfaces\IQueryBuilder,
    QueryBuilder};
use DDA58\MySQLHandlerForLaravelQueryBuilder\Services\{Grammar\Grammar,
    Grammar\IGrammar,
    Manager\HandlerManager,
    Manager\IHandlerManager,
    Processor\IProcessor,
    Processor\Processor};
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

/**
 * class HandlerServiceProvider
 * @package DDA58\MySQLHandlerForLaravelQueryBuilder
 */
class HandlerServiceProvider extends ServiceProvider
{
    public array $bindings = [
        IQueryBuilder::class => QueryBuilder::class,
        IEloquentBuilder::class => EloquentBuilder::class,
        IProcessor::class => Processor::class,
    ];

    public array $singletons = [
        IHandlerContainer::class => HandlerContainer::class,
        IGrammar::class => Grammar::class,
        IHandlerManager::class => HandlerManager::class,
    ];

    public function boot(IHandlerManager $handlerManager): void
    {
        $handlerManager->registerHandlerMethodsForQueryBuilder();
        $handlerManager->registerHandlerMethodsForEloquentBuilder();
    }

    public function register(): void
    {
        $this->app->singleton(
            'mysql.handler.manager',
            static fn(Application $app): IHandlerManager => $app->get(IHandlerManager::class)
        );

        $this->app->singleton(
            'mysql.handler.container',
            static fn(Application $app): IHandlerContainer => $app->get(IHandlerContainer::class)
        );

        $this->app->tag(
            [IQueryBuilder::class, IEloquentBuilder::class],
            ['mysql.handler.builder']
        );
    }
}
