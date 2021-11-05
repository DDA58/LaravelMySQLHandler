<?php

declare(strict_types=1);

namespace DDA58\LaravelMySQLHandler\Exceptions;

use InvalidArgumentException;

/**
 * @package DDA58\LaravelMySQLHandler
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
