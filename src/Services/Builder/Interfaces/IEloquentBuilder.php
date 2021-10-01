<?php

declare(strict_types=1);

namespace DDA58\MySQLHandlerForLaravelQueryBuilder\Services\Builder\Interfaces;

use Illuminate\Database\Eloquent\Builder as DbEloquentBuilder;
use Illuminate\Database\Query\Builder as DbQueryBuilder;
use Illuminate\Support\Collection;

/**
 * Interface IEloquentBuilder
 * @package DDA58\MySQLHandlerForLaravelQueryBuilder
 */
interface IEloquentBuilder extends IBuilder
{
    public function getDatabaseQueryBuilder(): DbQueryBuilder;

    public function get(): Collection;

    public function getDbBuilder(): DbEloquentBuilder;

    public function setDbBuilder(DbEloquentBuilder $dbBuilder): void;
}
