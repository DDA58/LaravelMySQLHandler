<?php

declare(strict_types=1);

namespace DDA58\LaravelMySQLHandler\Services\Processor;

use PDO;

/**
 * @package DDA58\LaravelMySQLHandler
 */
interface IProcessor
{
    public function setPdo(PDO $pdo): void;

    public function executeOpen(string $command): bool;

    public function executeClose(string $command): bool;

    public function executeRead(string $command, array $bindings = []): array;
}
