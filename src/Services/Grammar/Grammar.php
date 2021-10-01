<?php

declare(strict_types=1);

namespace DDA58\MySQLHandlerForLaravelQueryBuilder\Services\Grammar;

use DDA58\MySQLHandlerForLaravelQueryBuilder\Exceptions\KeywordNeedsIndexValue;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Exceptions\KeywordNotAllowed;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Exceptions\KeywordNotAllowedWithoutIndexName;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Exceptions\KeywordNotAllowedForIndexName;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Services\Builder\Interfaces\IBuilder;

/**
 * class Grammar
 * @package DDA58\MySQLHandlerForLaravelQueryBuilder
 */
class Grammar implements IGrammar
{
    /**
     * @var array
     */
    private const ALLOWED_COMPARISON_SYMBOLS = ['=', '<=', '>=', '<', '>'];

    /**
     * @var array
     */
    private const ALLOWED_KEYWORDS_FOR_INDEX = ['FIRST', 'NEXT', 'PREV', 'LAST'];

    /**
     * @var array
     */
    private const ALLOWED_KEYWORDS_FOR_HANDLER = ['FIRST', 'NEXT'];

    /**
     * @var string[]
     */
    private array $allowedComparisonSymbolsFlipped;
    /**
     * @var string[]
     */
    private array $allowedKeyWordsForIndexFlipped;
    /**
     * @var string[]
     */
    private array $allowedKeyWordsForHandlerFlipped;

    public function __construct()
    {
        $this->allowedComparisonSymbolsFlipped = array_flip($this->getAllowedComparisonSymbols());
        $this->allowedKeyWordsForIndexFlipped = array_flip($this->getAllowedKeyWordsForIndex());
        $this->allowedKeyWordsForHandlerFlipped = array_flip($this->getAllowedKeyWordsForHandler());
    }

    /**
     * Get allowed comparison symbols for handler when it is using with index name
     *
     * @return array
     */
    public function getAllowedComparisonSymbols(): array
    {
        return static::ALLOWED_COMPARISON_SYMBOLS;
    }

    /**
     * Get allowed keywords for handler when it is using without index name
     *
     * @return array
     */
    public function getAllowedKeyWordsForHandler(): array
    {
        return static::ALLOWED_KEYWORDS_FOR_HANDLER;
    }

    /**
     * Get allowed keywords for handler when it is using with index name
     *
     * @return array
     */
    public function getAllowedKeyWordsForIndex(): array
    {
        return static::ALLOWED_KEYWORDS_FOR_INDEX;
    }

    private function compileBase(IBuilder $handlerBuilder, string $handlerBody): string
    {
        $handlerName = $handlerBuilder->getHandlerName();
        $limit = '';
        $offset = '';
        $where = $this->compileWheres($handlerBuilder);

        if ($handlerBuilder->getLimit()) {
            $limit = $this->compileLimit($handlerBuilder->getLimit());

            if ($handlerBuilder->getOffset()) {
                $offset = $this->compileOffset($handlerBuilder->getOffset());
            }
        }

        //HANDLER `{handler_name}` READ
        $command = 'HANDLER `' . $handlerName . '` READ ';

        //HANDLER `{handler_name}` READ ... WHERE ... LIMIT ... OFFSET
        $command .= $handlerBody . $where . ' ' . $limit . ' ' . $offset;

        return trim($command);
    }

    public function compileReadPrimary(IBuilder $handlerBuilder): string
    {
        $indexValue = $handlerBuilder->getIndexValue();
        $keyword = $handlerBuilder->getKeyword();
        //`{index_name}`
        $command = '`PRIMARY` ';

        if (array_key_exists($keyword, $this->allowedComparisonSymbolsFlipped)) {
            //`{index_name}` { = | <= | >= | < | > } (?)
            $command .= $keyword . ' (' . trim(str_repeat('?,', count($indexValue)), ',') . ') ';
        } elseif (array_key_exists($keyword, $this->allowedKeyWordsForIndexFlipped)) {
            //`{index_name}` { FIRST | NEXT | PREV | LAST }
            $command .= $keyword . ' ';
        } else {
            throw new KeywordNotAllowed($keyword);
        }

        return $this->compileBase($handlerBuilder, $command);
    }

    public function compileReadPrev(IBuilder $handlerBuilder): string
    {
        return $this->compileBase(
            $handlerBuilder,
            '`' . $handlerBuilder->getIndexName() . '` PREV '
        );
    }

    public function compileReadNext(IBuilder $handlerBuilder): string
    {
        return $this->compileBase(
            $handlerBuilder,
            ($handlerBuilder->getIndexName() ? '`' . $handlerBuilder->getIndexName() . '` ' : '') . 'NEXT '
        );
    }

    public function compileReadLast(IBuilder $handlerBuilder): string
    {
        return $this->compileBase(
            $handlerBuilder,
            '`' . $handlerBuilder->getIndexName() . '` LAST '
        );
    }

    public function compileReadFirst(IBuilder $handlerBuilder): string
    {
        return $this->compileBase(
            $handlerBuilder,
            ($handlerBuilder->getIndexName() ? '`' . $handlerBuilder->getIndexName() . '` ' : '') . 'FIRST '
        );
    }

    public function compileRead(IBuilder $handlerBuilder): string
    {
        $indexName = $handlerBuilder->getIndexName();
        $indexValue = $handlerBuilder->getIndexValue();
        $keyword = $handlerBuilder->getKeyword();
        $command = '';

        if ($indexName) {
            //`{index_name}`
            $command .= '`' . $indexName . '` ';
            if (array_key_exists($keyword, $this->allowedComparisonSymbolsFlipped)) {
                if ($indexValue) {
                    //`{index_name}` { = | <= | >= | < | > } (?)
                    $command .= $keyword . ' (' . trim(str_repeat('?,', count($indexValue)), ',') . ') ';
                } else {
                    throw new KeywordNeedsIndexValue($keyword);
                }
            } elseif (array_key_exists($keyword, $this->allowedKeyWordsForIndexFlipped)) {
                //`{index_name}` { FIRST | NEXT | PREV | LAST }
                $command .= $keyword . ' ';
            } else {
                throw new KeywordNotAllowedForIndexName($keyword, $indexName);
            }
        } else {
            if (!array_key_exists($keyword, $this->allowedKeyWordsForHandlerFlipped)) {
                throw new KeywordNotAllowedWithoutIndexName($keyword);
            }
            //HANDLER `{handler_name}` READ { FIRST | NEXT }
            $command .= $keyword . ' ';
        }

        return $this->compileBase($handlerBuilder, $command);
    }

    public function compileLimit(int $limit): string
    {
        return 'LIMIT ' . $limit;
    }

    public function compileOffset(int $offset): string
    {
        return 'OFFSET ' . $offset;
    }

    public function compileWheres(IBuilder $handlerBuilder): string
    {
        return $handlerBuilder->getDatabaseQueryBuilder()->getGrammar()->compileWheres(
            $handlerBuilder->getDatabaseQueryBuilder()
        );
    }

    public function compileOpen(string $tableName, string $handlerName): string
    {
        return 'HANDLER `' . $tableName . '` OPEN AS `' . $handlerName . '`';
    }

    public function compileClose(string $handlerName): string
    {
        return 'HANDLER `' . $handlerName . '` CLOSE';
    }
}
