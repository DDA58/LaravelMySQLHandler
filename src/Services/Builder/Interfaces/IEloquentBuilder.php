<?php

declare(strict_types=1);

namespace DDA58\LaravelMySQLHandler\Services\Builder\Interfaces;

use Illuminate\Database\Eloquent\Builder as DbEloquentBuilder;
use Illuminate\Database\Query\Builder as DbQueryBuilder;
use Illuminate\Support\Collection;

/**
 * @package DDA58\LaravelMySQLHandler
 */
interface IEloquentBuilder extends IBuilder
{
    public function getDatabaseQueryBuilder(): DbQueryBuilder;

    public function get(): Collection;

    public function getDbBuilder(): DbEloquentBuilder;

    public function setDbBuilder(DbEloquentBuilder $dbBuilder): void;
}
