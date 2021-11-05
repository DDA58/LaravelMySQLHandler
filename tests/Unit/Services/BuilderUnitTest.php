<?php

declare(strict_types=1);

namespace DDA58\LaravelMySQLHandler\Tests\Unit\Services;

use DDA58\LaravelMySQLHandler\Container\IHandlerContainer;
use DDA58\LaravelMySQLHandler\Services\Builder\ABuilder;
use DDA58\LaravelMySQLHandler\Services\Builder\Interfaces\IBuilder;
use DDA58\LaravelMySQLHandler\Services\Grammar\IGrammar;
use DDA58\LaravelMySQLHandler\Services\Processor\IProcessor;
use DDA58\LaravelMySQLHandler\Tests\ABaseTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class BuilderUnitTest extends ABaseTestCase
{
    private IBuilder $handlerBuilder;
    /** @var IGrammar&MockObject */
    private IGrammar $grammar;
    /** @var IHandlerContainer&MockObject */
    private IHandlerContainer $repository;
    /** @var IProcessor&MockObject */
    private IProcessor $processor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->grammar = $this->createMock(IGrammar::class);
        $this->repository = $this->createMock(IHandlerContainer::class);
        $this->processor = $this->createMock(IProcessor::class);
        $this->handlerBuilder = $this->getMockForAbstractClass(
            ABuilder::class,
            [
                $this->grammar,
                $this->repository,
                $this->processor
            ]
        );
    }

    public function testSuccessInstanceOf(): void
    {
        $this->assertInstanceOf(
            IBuilder::class,
            $this->handlerBuilder
        );
    }

    public function testSuccessOpenWhenRepositoryNotContainHandlerName(): void
    {
        $handlerName = $this->faker->word;
        $this->repository
            ->expects(self::once())
            ->method('containHandlerName')
            ->willReturn(false);
        $this->repository
            ->expects(self::once())
            ->method('add');
        $this->processor
            ->expects(self::once())
            ->method('executeOpen');
        $this->grammar
            ->expects(self::once())
            ->method('compileOpen');
        $this->handlerBuilder
            ->expects(self::exactly(2))
            ->method('getDatabaseQueryBuilder');

        $result = $this->handlerBuilder->open($handlerName);

        $this->assertInstanceOf(IBuilder::class, $result);
        $this->assertEquals($handlerName, $result->getHandlerName());
    }

    public function testSuccessOpenWhenRepositoryContainHandlerName(): void
    {
        $handlerName = $this->faker->word;
        $this->repository
            ->expects(self::once())
            ->method('containHandlerName')
            ->willReturn(true);
        $this->repository
            ->expects(self::once())
            ->method('add');
        $this->processor
            ->expects(self::never())
            ->method('executeOpen');
        $this->grammar
            ->expects(self::never())
            ->method('compileOpen');
        $this->handlerBuilder
            ->expects(self::exactly(2))
            ->method('getDatabaseQueryBuilder');

        $result = $this->handlerBuilder->open($handlerName);

        $this->assertInstanceOf(IBuilder::class, $result);
        $this->assertEquals($handlerName, $result->getHandlerName());
    }

    public function testSuccessCloseWhenRepositoryContainHandlerName(): void
    {
        $this->handlerBuilder->setHandlerName($this->faker->word);
        $this->repository
            ->expects(self::once())
            ->method('containHandlerName')
            ->willReturn(true);
        $this->repository
            ->expects(self::once())
            ->method('remove');
        $this->processor
            ->expects(self::once())
            ->method('executeClose');
        $this->grammar
            ->expects(self::once())
            ->method('compileClose');
        $this->handlerBuilder
            ->expects(self::once())
            ->method('getDatabaseQueryBuilder');

        $this->handlerBuilder->close();
    }

    public function testSuccessCloseWhenRepositoryNotContainHandlerName(): void
    {
        $this->handlerBuilder->setHandlerName($this->faker->word);
        $this->repository
            ->expects(self::once())
            ->method('containHandlerName')
            ->willReturn(false);
        $this->repository
            ->expects(self::never())
            ->method('remove');
        $this->processor
            ->expects(self::never())
            ->method('executeClose');
        $this->grammar
            ->expects(self::never())
            ->method('compileClose');
        $this->handlerBuilder
            ->expects(self::never())
            ->method('getDatabaseQueryBuilder');

        $this->handlerBuilder->close();
    }

    public function testSuccessReadPrimary(): void
    {
        $indexValue = $this->faker->word;
        $this->grammar
            ->expects(self::once())
            ->method('compileReadPrimary');

        $result = $this->handlerBuilder->readPrimary($indexValue);

        $this->assertInstanceOf(IBuilder::class, $result);
        $this->assertEquals('PRIMARY', $result->getIndexName());
        $this->assertEquals([$indexValue], $result->getIndexValue());
    }

    public function testSuccessReadPrev(): void
    {
        $indexName = $this->faker->word;
        $this->grammar
            ->expects(self::once())
            ->method('compileReadPrev');

        $result = $this->handlerBuilder->readPrev($indexName);

        $this->assertInstanceOf(IBuilder::class, $result);
        $this->assertEquals('PREV', $result->getKeyword());
        $this->assertEquals($indexName, $result->getIndexName());
    }

    public function testSuccessReadNext(): void
    {
        $indexName = $this->faker->word;
        $this->grammar
            ->expects(self::once())
            ->method('compileReadNext');

        $result = $this->handlerBuilder->readNext($indexName);

        $this->assertInstanceOf(IBuilder::class, $result);
        $this->assertEquals('NEXT', $result->getKeyword());
        $this->assertEquals($indexName, $result->getIndexName());
    }

    public function testSuccessReadLast(): void
    {
        $indexName = $this->faker->word;
        $this->grammar
            ->expects(self::once())
            ->method('compileReadLast');

        $result = $this->handlerBuilder->readLast($indexName);

        $this->assertInstanceOf(IBuilder::class, $result);
        $this->assertEquals('LAST', $result->getKeyword());
        $this->assertEquals($indexName, $result->getIndexName());
    }

    public function testSuccessReadFirst(): void
    {
        $indexName = $this->faker->word;
        $this->grammar
            ->expects(self::once())
            ->method('compileReadFirst');

        $result = $this->handlerBuilder->readFirst($indexName);

        $this->assertInstanceOf(IBuilder::class, $result);
        $this->assertEquals('FIRST', $result->getKeyword());
        $this->assertEquals($indexName, $result->getIndexName());
    }

    public function testSuccessRead(): void
    {
        $indexName = $this->faker->word;
        $indexValue = [$this->faker->word];
        $this->grammar
            ->expects(self::once())
            ->method('compileRead');

        $result = $this->handlerBuilder->read(
            $indexName,
            $indexValue
        );

        $this->assertInstanceOf(IBuilder::class, $result);
        $this->assertEquals($indexValue, $result->getIndexValue());
        $this->assertEquals($indexName, $result->getIndexName());
    }

    public function testSuccessLimit(): void
    {
        $this->handlerBuilder
            ->expects(self::once())
            ->method('getDatabaseQueryBuilder');

        $result = $this->handlerBuilder->limit($this->faker->randomDigit);

        $this->assertInstanceOf(IBuilder::class, $result);
    }

    public function testSuccessOffset(): void
    {
        $this->handlerBuilder
            ->expects(self::once())
            ->method('getDatabaseQueryBuilder');

        $result = $this->handlerBuilder->offset($this->faker->randomDigit);

        $this->assertInstanceOf(IBuilder::class, $result);
    }

    public function testSuccessWhere(): void
    {
        $args = [
            $this->faker->word,
            '=',
            $this->faker->word
        ];
        $this->handlerBuilder
            ->expects(self::once())
            ->method('getDatabaseQueryBuilder');

        $result = $this->handlerBuilder->where(...$args);

        $this->assertInstanceOf(IBuilder::class, $result);
    }
}
