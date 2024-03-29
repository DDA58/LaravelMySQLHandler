<?php

declare(strict_types=1);

namespace DDA58\LaravelMySQLHandler\Services\Grammar;

use DDA58\LaravelMySQLHandler\Services\Builder\Interfaces\IBuilder;

/**
 * @package DDA58\LaravelMySQLHandler
 */
interface IGrammar
{
    public function getAllowedComparisonSymbols(): array;

    public function getAllowedKeyWordsForHandler(): array;

    public function getAllowedKeyWordsForIndex(): array;

    public function compileReadPrimary(IBuilder $handlerBuilder): string;

    public function compileReadPrev(IBuilder $handlerBuilder): string;

    public function compileReadNext(IBuilder $handlerBuilder): string;

    public function compileReadLast(IBuilder $handlerBuilder): string;

    public function compileReadFirst(IBuilder $handlerBuilder): string;

    public function compileRead(IBuilder $handlerBuilder): string;

    public function compileLimit(int $limit): string;

    public function compileOffset(int $offset): string;

    public function compileWheres(IBuilder $handlerBuilder): string;

    public function compileOpen(string $tableName, string $handlerName): string;

    public function compileClose(string $handlerName): string;
}
