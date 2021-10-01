<?php

declare(strict_types=1);

namespace DDA58\MySQLHandlerForLaravelQueryBuilder\Services\Builder\Interfaces;

interface IBuilder
{
    public function getHandlerName(): string;

    public function setHandlerName(string $handlerName): self;

    public function getIndexName(): ?string;

    public function setIndexName(string $indexName): void;

    public function getIndexValue(): array;

    public function setIndexValue(array $indexValue): void;

    public function getKeyword(): string;

    public function setKeyword(string $keyword): void;

    public function open(string $alias = ''): self;

    public function close(): void;

    public function readPrimary($indexValue, string $keyword = '='): self;

    public function readPrev(string $indexName): self;

    public function readNext(?string $indexName = null): self;

    public function readLast(string $indexName): self;

    public function readFirst(?string $indexName = null): self;

    public function read(?string $indexName = null, $indexValue = [], ?string $keyword = '='): self;

    public function limit(int $limit): self;

    public function offset(int $offset): self;

    public function where(...$args): self;

    public function getLimit(): ?int;

    public function getOffset(): ?int;

    public function getBindings(): array;

    public function getBindingsValues(): array;

    public function resetCompiledCommand(): void;
}
