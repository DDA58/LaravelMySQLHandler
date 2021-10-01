<?php

declare(strict_types=1);

namespace DDA58\MySQLHandlerForLaravelQueryBuilder\Services\Processor;

use Illuminate\Database\Query\Builder as DbBuilder;
use PDO;

/**
 * class Processor
 * @package DDA58\MySQLHandlerForLaravelQueryBuilder
 */
class Processor implements IProcessor
{
    private PDO $pdo;

    public function __construct(DbBuilder $dbBuilder)
    {
        $this->pdo = $dbBuilder->getConnection()->getPdo();
    }

    public function setPdo(PDO $pdo): void
    {
        $this->pdo = $pdo;
    }

    public function executeOpen(string $command): bool
    {
        return $this->pdo->prepare($command)->execute();
    }

    public function executeClose(string $command): bool
    {
        return $this->pdo->prepare($command)->execute();
    }

    public function executeRead(string $command, array $bindings = []): array
    {
        $query = $this->pdo->prepare($command);
        $query->execute($bindings);
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        return $result ?: [];
    }
}
