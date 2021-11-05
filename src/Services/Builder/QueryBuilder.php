<?php

declare(strict_types=1);

namespace DDA58\LaravelMySQLHandler\Services\Builder;

use DDA58\LaravelMySQLHandler\Container\IHandlerContainer;
use DDA58\LaravelMySQLHandler\Services\Builder\Interfaces\IQueryBuilder;
use DDA58\LaravelMySQLHandler\Services\Grammar\IGrammar;
use DDA58\LaravelMySQLHandler\Services\Processor\IProcessor;
use Illuminate\Database\Query\Builder as DbQueryBuilder;
use Illuminate\Support\Collection;

/**
 * @package DDA58\LaravelMySQLHandler
 */
class QueryBuilder extends ABuilder implements IQueryBuilder
{
    private DbQueryBuilder $dbBuilder;

    public function __construct(
        IGrammar $grammar,
        IHandlerContainer $handlerRepository,
        IProcessor $processor,
        DbQueryBuilder $dbBuilder
    ) {
        parent::__construct($grammar, $handlerRepository, $processor);

        $this->dbBuilder = $dbBuilder;
        $this->processor->setPdo($dbBuilder->getConnection()->getPdo());
    }

    public function getDatabaseQueryBuilder(): DbQueryBuilder
    {
        return $this->dbBuilder;
    }

    public function getDbBuilder(): DbQueryBuilder
    {
        return $this->dbBuilder;
    }

    public function setDbBuilder(DbQueryBuilder $dbBuilder): void
    {
        $this->dbBuilder = $dbBuilder;
    }

    public function get(): Collection
    {
        return new Collection(
            $this->getArray()
        );
    }
}
