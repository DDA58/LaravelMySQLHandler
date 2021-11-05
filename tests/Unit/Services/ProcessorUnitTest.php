<?php

declare(strict_types=1);

namespace DDA58\LaravelMySQLHandler\Tests\Unit\Services;

use DDA58\LaravelMySQLHandler\Services\Processor\IProcessor;
use DDA58\LaravelMySQLHandler\Services\Processor\Processor;
use DDA58\LaravelMySQLHandler\Tests\ABaseTestCase;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder as DbBuilder;

class ProcessorUnitTest extends ABaseTestCase
{
    private IProcessor $processor;

    protected function setUp(): void
    {
        parent::setUp();

        $dbBuilder = $this->createMock(DbBuilder::class);
        $connection = $this->createMock(Connection::class);
        $connection
            ->method('getPdo')
            ->willReturn($this->mockedPdo);
        $dbBuilder
            ->method('getConnection')
            ->willReturn($connection);

        $this->processor = new Processor(
            $dbBuilder
        );
    }

    public function testSuccessInstanceOf(): void
    {
        $this->assertInstanceOf(
            IProcessor::class,
            $this->processor
        );
    }

    public function testSuccessExecuteOpen(): void
    {
        $expected = $this->faker->boolean;

        $this->mockedPdoStatement
            ->expects(self::once())
            ->method('execute')
            ->withAnyParameters()
            ->willReturn($expected);
        $this->mockedPdo
            ->expects(self::once())
            ->method('prepare')
            ->withAnyParameters()
            ->willReturn($this->mockedPdoStatement);

        $result = $this->processor->executeOpen($this->faker->sentence);

        $this->assertEquals($expected, $result);
    }

    public function testSuccessExecuteClose(): void
    {
        $expected = $this->faker->boolean;

        $this->mockedPdoStatement
            ->expects(self::once())
            ->method('execute')
            ->withAnyParameters()
            ->willReturn($expected);
        $this->mockedPdo
            ->expects(self::once())
            ->method('prepare')
            ->withAnyParameters()
            ->willReturn($this->mockedPdoStatement);

        $result = $this->processor->executeClose($this->faker->sentence);

        $this->assertEquals($expected, $result);
    }

    public function testSuccessExecuteRead(): void
    {
        $expected = $this->faker->randomElements;

        $this->mockedPdoStatement
            ->expects(self::once())
            ->method('execute')
            ->withAnyParameters();
        $this->mockedPdoStatement
            ->expects(self::once())
            ->method('fetchAll')
            ->withAnyParameters()
            ->willReturn($expected);
        $this->mockedPdo
            ->expects(self::once())
            ->method('prepare')
            ->withAnyParameters()
            ->willReturn($this->mockedPdoStatement);

        $result = $this->processor->executeRead($this->faker->sentence, $this->faker->randomElements);

        $this->assertEquals($expected, $result);
    }
}
