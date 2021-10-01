<?php

declare(strict_types=1);

namespace DDA58\MySQLHandlerForLaravelQueryBuilder\Exceptions;

use Exception;

/**
 * class HandlerBuilderNotFound
 * @package DDA58\MySQLHandlerForLaravelQueryBuilder
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
