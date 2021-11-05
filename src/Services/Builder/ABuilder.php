<?php

declare(strict_types=1);

namespace DDA58\LaravelMySQLHandler\Services\Builder;

use Closure as Closure;
use DDA58\LaravelMySQLHandler\Container\IHandlerContainer;
use DDA58\LaravelMySQLHandler\Services\Builder\Interfaces\IBuilder;
use DDA58\LaravelMySQLHandler\Services\Grammar\IGrammar;
use DDA58\LaravelMySQLHandler\Services\Processor\IProcessor;
use Illuminate\Database\Query\Builder as DbQueryBuilder;
use Illuminate\Support\Collection;

/**
 * @package DDA58\LaravelMySQLHandler
 */
abstract class ABuilder implements IBuilder
{
    protected IGrammar $grammar;
    protected IHandlerContainer $handlerRepository;
    protected IProcessor $processor;
    protected string $handlerName;
    protected ?string $indexName;
    protected array $indexValue = [];
    protected string $keyword;
    protected string $compiledCommand = '';

    public function __construct(
        IGrammar $grammar,
        IHandlerContainer $handlerRepository,
        IProcessor $processor
    ) {
        $this->grammar = $grammar;
        $this->handlerRepository = $handlerRepository;
        $this->processor = $processor;
    }

    abstract public function getDatabaseQueryBuilder(): DbQueryBuilder;

    abstract public function get(): Collection;

    public function getHandlerName(): string
    {
        return $this->handlerName;
    }

    public function setHandlerName(string $handlerName): self
    {
        $this->handlerName = $handlerName;

        return $this;
    }

    public function getIndexName(): ?string
    {
        return $this->indexName;
    }

    public function setIndexName(string $indexName): void
    {
        $this->indexName = $indexName;
    }


    public function getIndexValue(): array
    {
        return $this->indexValue;
    }

    public function setIndexValue(array $indexValue): void
    {
        $this->indexValue = $indexValue;
    }

    public function getKeyword(): string
    {
        return $this->keyword;
    }

    public function setKeyword(string $keyword): void
    {
        $this->keyword = $keyword;
    }

    public function open(string $alias = ''): self
    {
        $tableName = $this->getDatabaseQueryBuilder()->from ?? $alias;
        $handlerName = $alias ?: $tableName;

        if (!$this->handlerRepository->containHandlerName($handlerName)) {
            $this->processor->executeOpen(
                $this->grammar->compileOpen($tableName, $handlerName)
            );
        }

        $this->handlerName = $handlerName;
        $this->handlerRepository->add($this->getDatabaseQueryBuilder(), $this);

        return $this;
    }

    public function close(): void
    {
        if ($this->handlerRepository->containHandlerName($this->handlerName)) {
            $this->processor->executeClose(
                $this->grammar->compileClose($this->handlerName)
            );
            $this->handlerRepository->remove($this->getDatabaseQueryBuilder());
        }
    }

    /**
     * @param array|string|int $indexValue
     * @param string $keyword
     * @return $this
     */
    public function readPrimary($indexValue, string $keyword = '='): self
    {
        $this->indexName = 'PRIMARY';
        $this->indexValue = is_array($indexValue) ? $indexValue : [$indexValue];
        $this->keyword = $keyword;

        if (!$this->compiledCommand) {
            $this->compiledCommand = $this->grammar->compileReadPrimary($this);
        }

        return $this;
    }

    public function readPrev(string $indexName): self
    {
        $this->indexName = $indexName;
        $this->keyword = 'PREV';

        if (!$this->compiledCommand) {
            $this->compiledCommand = $this->grammar->compileReadPrev($this);
        }

        return $this;
    }

    public function readNext(?string $indexName = null): self
    {
        $this->indexName = $indexName;
        $this->keyword = 'NEXT';

        if (!$this->compiledCommand) {
            $this->compiledCommand = $this->grammar->compileReadNext($this);
        }

        return $this;
    }

    public function readLast(string $indexName): self
    {
        $this->indexName = $indexName;
        $this->keyword = 'LAST';

        if (!$this->compiledCommand) {
            $this->compiledCommand = $this->grammar->compileReadLast($this);
        }

        return $this;
    }

    public function readFirst(?string $indexName = null): self
    {
        $this->indexName = $indexName;
        $this->keyword = 'FIRST';

        if (!$this->compiledCommand) {
            $this->compiledCommand = $this->grammar->compileReadFirst($this);
        }

        return $this;
    }

    /**
     * @param string|null $indexName
     * @param array|string|int $indexValue
     * @param string|null $keyword
     * @return $this
     */
    public function read(?string $indexName = null, $indexValue = [], ?string $keyword = '='): self
    {
        $this->indexName = $indexName;
        $this->indexValue = is_array($indexValue) ? $indexValue : [$indexValue];
        $this->keyword = $keyword;

        if (!$this->compiledCommand) {
            $this->compiledCommand = $this->grammar->compileRead($this);
        }

        return $this;
    }

    protected function getArray(): array
    {
        return $this->processor->executeRead(
            $this->compiledCommand,
            $this->getBindingsValues()
        );
    }

    public function limit(int $limit): self
    {
        $this->getDatabaseQueryBuilder()->limit($limit);
        $this->resetCompiledCommand();

        return $this;
    }

    public function offset(int $offset): self
    {
        $this->getDatabaseQueryBuilder()->offset($offset);
        $this->resetCompiledCommand();

        return $this;
    }

    /**
     * @param Closure|string|array $column
     * @param mixed $operator
     * @param mixed $value
     * @param string $boolean
     * @return $this
     */
    public function where(...$args): self
    {
        $this->getDatabaseQueryBuilder()->where(...$args);
        $this->resetCompiledCommand();

        return $this;
    }

    public function getLimit(): ?int
    {
        return $this->getDatabaseQueryBuilder()->limit;
    }

    public function getOffset(): ?int
    {
        return $this->getDatabaseQueryBuilder()->offset;
    }

    public function getBindings(): array
    {
        return [
            'index_value' => $this->indexValue,
            'where' => $this->getDatabaseQueryBuilder()->bindings['where']
        ];
    }

    public function getBindingsValues(): array
    {
        return [
            ...$this->indexValue,
            ...$this->getDatabaseQueryBuilder()->bindings['where']
        ];
    }

    public function resetCompiledCommand(): void
    {
        $this->compiledCommand = '';
    }
}
