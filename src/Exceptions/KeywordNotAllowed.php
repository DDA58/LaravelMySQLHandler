<?php

declare(strict_types=1);

namespace DDA58\MySQLHandlerForLaravelQueryBuilder\Exceptions;

use InvalidArgumentException;

/**
 * class KeywordNotAllowed
 * @package DDA58\MySQLHandlerForLaravelQueryBuilder
 */
class KeywordNotAllowed extends InvalidArgumentException
{
    public function __construct(string $keyword)
    {
        parent::__construct(
            sprintf('Keyword "%s" not allowed for MySQL handler statement', $keyword)
        );
    }
}
