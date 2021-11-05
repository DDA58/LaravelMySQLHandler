<?php

declare(strict_types=1);

namespace DDA58\LaravelMySQLHandler\Exceptions;

use Exception;

/**
 * @package DDA58\LaravelMySQLHandler
 */
class HandlerBuilderNotFound extends Exception
{
    public function __construct(string $handlerName)
    {
        parent::__construct(
            sprintf(
                'Handler builder not found in HandlerRepositoryInterface for handler name "%s"',
                $handlerName
            )
        );
    }
}
