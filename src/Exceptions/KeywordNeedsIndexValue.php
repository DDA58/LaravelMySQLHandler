<?php

declare(strict_types=1);

namespace DDA58\MySQLHandlerForLaravelQueryBuilder\Exceptions;

use InvalidArgumentException;

/**
 * class KeywordNeedsIndexValue
 * @package DDA58\MySQLHandlerForLaravelQueryBuilder
 */
class KeywordNeedsIndexValue extends InvalidArgumentException
{
    public function __construct(string $keyword)
    {
        parent::__construct(
            sprintf('Keyword "%s" needs index value', $keyword)
        );
    }
}
