<?php

declare(strict_types=1);

namespace DDA58\MySQLHandlerForLaravelQueryBuilder\Services\Builder;

use DDA58\MySQLHandlerForLaravelQueryBuilder\Container\IHandlerContainer;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Services\Builder\Interfaces\IEloquentBuilder;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Services\Grammar\IGrammar;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Services\Processor\IProcessor;
use Illuminate\Database\Eloquent\Builder as DbEloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as DbQueryBuilder;
use Illuminate\Support\Collection;

/**
 * class EloquentBuilder
 * @package DDA58\MySQLHandlerForLaravelQueryBuilder
 */
class EloquentBuilder extends ABuilder implements IEloquentBuilder
{
    private DbEloquentBuilder $dbBuilder;

    public function __construct(
        IGrammar $grammar,
        IHandlerContainer $handlerRepository,
        IProcessor $processor,
        DbEloquentBuilder $dbBuilder
    ) {
        parent::__construct($grammar, $handlerRepository, $processor);

        $this->dbBuilder = $dbBuilder;
        $this->processor->setPdo($dbBuilder->getQuery()->getConnection()->getPdo());
    }

    public function getDatabaseQueryBuilder(): DbQueryBuilder
    {
        return $this->dbBuilder->getQuery();
    }

    public function getDbBuilder(): DbEloquentBuilder
    {
        return $this->dbBuilder;
    }

    public function setDbBuilder(DbEloquentBuilder $dbBuilder): void
    {
        $this->dbBuilder = $dbBuilder;
    }

    public function get(): Collection
    {
        $builder = $this->dbBuilder->applyScopes();

        // If we actually found models we will also eager load any relationships that
        // have been specified as needing to be eager loaded, which will solve the
        // n+1 query issue for the developers to avoid running a lot of queries.
//        TODO Check it
//        if (count($models = $this->getModels()) > 0) {
//            $models = $builder->eagerLoadRelations($models);
//        }

        $models = $this->getModels();

        return $builder->getModel()->newCollection($models);
    }

    /**
     * Get the hydrated models without eager loading.
     *
     * @return Model[]
     */
    public function getModels(): array
    {
        return $this->dbBuilder->getModel()->hydrate(
            $this->getArray()
        )->all();
    }
}
