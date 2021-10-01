<?php

declare(strict_types=1);

namespace DDA58\MySQLHandlerForLaravelQueryBuilder\Exceptions;

use InvalidArgumentException;

/**
 * class KeywordNotAllowedForIndexName
 * @package DDA58\MySQLHandlerForLaravelQueryBuilder
 */
class KeywordNotAllowedForIndexName extends InvalidArgumentException
{
    public function __construct(string $keyword, string $indexName)
    {
        parent::__construct(
            sprintf('Keyword "%s" not allowed for MySQL handler statement with index name "%s"', $keyword, $indexName)
        );
    }
}
