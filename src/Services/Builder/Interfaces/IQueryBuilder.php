<?php

declare(strict_types=1);

namespace DDA58\MySQLHandlerForLaravelQueryBuilder\Services\Builder\Interfaces;

use Illuminate\Database\Query\Builder as DbQueryBuilder;
use Illuminate\Support\Collection;

/**
 * Interface IQueryBuilder
 * @package DDA58\MySQLHandlerForLaravelQueryBuilder
 */
interface IQueryBuilder extends IBuilder
{
    public function getDatabaseQueryBuilder(): DbQueryBuilder;

    public function get(): Collection;

    public function getDbBuilder(): DbQueryBuilder;

    public function setDbBuilder(DbQueryBuilder $dbBuilder): void;
}
