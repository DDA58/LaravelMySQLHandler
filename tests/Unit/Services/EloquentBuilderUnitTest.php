<?php

declare(strict_types=1);

namespace DDA58\MySQLHandlerForLaravelQueryBuilder\Tests\Unit\Services;

use DDA58\MySQLHandlerForLaravelQueryBuilder\Container\IHandlerContainer;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Services\Builder\EloquentBuilder;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Services\Builder\Interfaces\IEloquentBuilder;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Services\Grammar\IGrammar;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Services\Processor\IProcessor;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Tests\ABaseTestCase;
use Illuminate\Database\Eloquent\Builder as DbEloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\Query\Builder as DbBuilder;
use Illuminate\Support\Collection;
use PHPUnit\Framework\MockObject\MockObject;

class EloquentBuilderUnitTest extends ABaseTestCase
{
    private IEloquentBuilder $handlerBuilder;
    /** @var IProcessor&MockObject */
    private IProcessor $processor;
    /** @var DbEloquentBuilder&MockObject */
    private DbEloquentBuilder $dbEloquentBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $grammar = $this->createMock(IGrammar::class);
        $repository = $this->createMock(IHandlerContainer::class);
        $this->dbEloquentBuilder = $this->createMock(DbEloquentBuilder::class);
        $dbQueryBuilder = $this->createMock(DbBuilder::class);
        $this->processor = $this->createMock(IProcessor::class);
        $connection = $this->createMock(MySqlConnection::class);
        $connection
            ->method('getPdo')
            ->willReturn($this->mockedPdo);
        $this->dbEloquentBuilder
            ->method('getQuery')
            ->willReturn($dbQueryBuilder);
        $dbQueryBuilder
            ->method('getConnection')
            ->willReturn($connection);

        $this->handlerBuilder = new EloquentBuilder(
            $grammar,
            $repository,
            $this->processor,
            $this->dbEloquentBuilder
        );
    }

    public function testSuccessInstanceOf(): void
    {
        $this->assertInstanceOf(
            IEloquentBuilder::class,
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
        $collectionValue = new Collection($value);
        $model = $this->getMockBuilder(Model::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->addMethods(['hydrate'])
            ->onlyMethods(['newCollection'])
            ->getMock();

        $this->dbEloquentBuilder
            ->expects(self::exactly(2))
            ->method('getModel')
            ->willReturn($model);
        $this->dbEloquentBuilder
            ->expects(self::once())
            ->method('applyScopes')
            ->willReturn($this->dbEloquentBuilder);
        $model
            ->expects(self::once())
            ->method('hydrate')
            ->willReturn($collectionValue);
        $model
            ->expects(self::once())
            ->method('newCollection')
            ->willReturn($collectionValue);
        $this->processor
            ->expects(self::once())
            ->method('executeRead')
            ->willReturn($value);

        $result = $this->handlerBuilder->get();

        $this->assertEquals($collectionValue, $result);
    }
}
