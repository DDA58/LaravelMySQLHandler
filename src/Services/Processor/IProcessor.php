<?php

declare(strict_types=1);

namespace DDA58\MySQLHandlerForLaravelQueryBuilder\Services\Processor;

use PDO;

/**
 * Interface IProcessor
 * @package DDA58\MySQLHandlerForLaravelQueryBuilder
 */
interface IProcessor
{
    public function setPdo(PDO $pdo): void;

    public function executeOpen(string $command): bool;

    public function executeClose(string $command): bool;

    public function executeRead(string $command, array $bindings = []): array;
}
