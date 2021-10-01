<?php

declare(strict_types=1);

namespace DDA58\MySQLHandlerForLaravelQueryBuilder\Tests;

use DDA58\MySQLHandlerForLaravelQueryBuilder\HandlerServiceProvider;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Database\Connectors\MySqlConnector;
use Illuminate\Database\DatabaseServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;

abstract class ABaseTestCase extends BaseTestCase
{
    /** @var PDO&MockObject */
    protected PDO $mockedPdo;
    /** @var PDOStatement&MockObject */
    protected PDOStatement $mockedPdoStatement;
    protected Generator $faker;

    protected function getPackageProviders($app): array
    {
        return [
            HandlerServiceProvider::class,
            DatabaseServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $this->mockedPdo = $this->createMock(PDO::class);
        $this->mockedPdoStatement = $this->createMock(PDOStatement::class);

        $connector = new class extends MySqlConnector {
            private PDO $mockedPdo;

            public function connect(array $config): PDO
            {
                return $this->mockedPdo;
            }

            public function setMockedPdo(PDO $pdo): void
            {
                $this->mockedPdo = $pdo;
            }
        };
        $connector->setMockedPdo($this->mockedPdo);

        $app->bind('db.connector.mysql', static fn(): MySqlConnector => $connector);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();
    }
}
