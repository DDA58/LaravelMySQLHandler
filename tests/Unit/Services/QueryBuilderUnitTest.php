<?php

declare(strict_types=1);

namespace DDA58\LaravelMySQLHandler\Tests\Unit\Services;

use DDA58\LaravelMySQLHandler\Container\IHandlerContainer;
use DDA58\LaravelMySQLHandler\Services\Builder\QueryBuilder;
use DDA58\LaravelMySQLHandler\Services\Builder\Interfaces\IQueryBuilder;
use DDA58\LaravelMySQLHandler\Services\Grammar\IGrammar;
use DDA58\LaravelMySQLHandler\Services\Processor\IProcessor;
use DDA58\LaravelMySQLHandler\Tests\ABaseTestCase;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\Query\Builder as DbBuilder;
use Illuminate\Support\Collection;
use PHPUnit\Framework\MockObject\MockObject;

class QueryBuilderUnitTest extends ABaseTestCase
{
    private IQueryBuilder $handlerBuilder;
    /** @var IProcessor&MockObject */
    private IProcessor $processor;

    protected function setUp(): void
    {
        parent::setUp();

        $grammar = $this->createMock(IGrammar::class);
        $repository = $this->createMock(IHandlerContainer::class);
        $dbBuilder = $this->createMock(DbBuilder::class);
        $this->processor = $this->createMock(IProcessor::class);
        $connection = $this->createMock(MySqlConnection::class);
        $connection
            ->method('getPdo')
            ->willReturn($this->mockedPdo);
        $dbBuilder
            ->method('getConnection')
            ->willReturn($connection);

        $this->handlerBuilder = new QueryBuilder(
            $grammar,
            $repository,
            $this->processor,
            $dbBuilder
        );
    }

    public function testSuccessInstanceOf(): void
    {
        $this->assertInstanceOf(
            IQueryBuilder::class,
            $this->handlerBuilder
        );
    }

    public function testSuccessGetDatabaseQueryBuilder(): void
    {
        $this->assertInstanceOf(
            DbBuilder::class,
            $this->handlerBuilder->getDatabaseQueryBuilder()
        );
    }


    public function testSuccessGet(): void
    {
        $value = $this->faker->randomElements();

        $this->processor
            ->expects(self::once())
            ->method('executeRead')
            ->willReturn($value);

        $result = $this->handlerBuilder->get();

        $this->assertEquals(new Collection($value), $result);
    }
}
