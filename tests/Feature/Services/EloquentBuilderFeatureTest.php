<?php

declare(strict_types=1);

namespace DDA58\LaravelMySQLHandler\Tests\Feature\Services;

use Closure;
use DDA58\LaravelMySQLHandler\Services\Builder\Interfaces\IEloquentBuilder;
use DDA58\LaravelMySQLHandler\Tests\ABaseTestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class EloquentBuilderFeatureTest extends ABaseTestCase
{
    private Model $model;
    private string $tableName;
    private string $columnName;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tableName = $this->faker->word;
        $this->columnName = $this->faker->word;
        $this->mockedPdoStatement
            ->method('execute')
            ->withAnyParameters()
            ->willReturn(true);
        $this->mockedPdo
            ->method('prepare')
            ->withAnyParameters()
            ->willReturn($this->mockedPdoStatement);

        $this->model = new class extends Model {
            protected $table = 'test';
        };
    }

    public function testSuccessOpenHandler(): void
    {
        $handler = $this->model->openHandler();

        $this->assertInstanceOf(IEloquentBuilder::class, $handler);
    }

    public function testSuccessGetHandlerFirst(): void
    {
        $this->model->openHandler();
        $handler = $this->model->getHandler($this->tableName);

        $this->assertInstanceOf(IEloquentBuilder::class, $handler);
    }

    public function testSuccessGetHandlerSecond(): void
    {
        $handler = $this->model->getHandler();

        $this->assertInstanceOf(IEloquentBuilder::class, $handler);
    }

    public function testSuccessGetHandlerThird(): void
    {
        $handler = $this->model->getHandler($this->tableName);

        $this->assertInstanceOf(IEloquentBuilder::class, $handler);
    }

    public function testSuccessCloseHandlerFirst(): void
    {
        /** @var IEloquentBuilder $handler */
        $handler = $this->model->openHandler();

        $this->assertNull($handler->close());
    }

    public function testSuccessCloseHandlerSecond(): void
    {
        $this->model->openHandler();

        $this->assertNull(
            $this->model->closeHandler()
        );
    }

    public function testSuccessCloseHandlerThird(): void
    {
        $this->model->openHandler();

        $this->assertNull(
            $this->model->closeHandler($this->tableName)
        );
    }

    private function getExpectedArray(): array
    {
        $expected = [];

        for ($i = 0; $i < $this->faker->randomDigitNotZero(); $i++) {
            $expected[] = [$this->columnName => $this->faker->word];
        }

        return $expected;
    }

    private function baseRead(Closure $getResult): void
    {
        /** @var IEloquentBuilder $handler */
        $handler = $this->model->openHandler();

        $expected = $this->getExpectedArray();
        $this->mockedPdoStatement
            ->expects(self::once())
            ->method('fetchAll')
            ->willReturn($expected);

        /** @var Collection $result */
        $result = $getResult($handler, $expected);

        $this->assertCount(count($expected), $result);
        $this->assertInstanceOf(Model::class, $result->first());
        $this->assertEquals(
            $expected[0][$this->columnName],
            $result->first()->{$this->columnName}
        );
        $this->assertEquals($this->model->getTable(), $result->first()->getTable());
    }

    public function testSuccessReadPrimary(): void
    {
        $getResult =
            fn(IEloquentBuilder $handler, array $expected): Collection => $handler
                ->readPrimary($this->faker->word)
                ->limit(count($expected))
                ->get();

        $this->baseRead($getResult);
    }

    public function testSuccessReadPrev(): void
    {
        $getResult =
            fn(IEloquentBuilder $handler, array $expected): Collection => $handler
                ->readPrev($this->faker->word)
                ->limit(count($expected))
                ->get();

        $this->baseRead($getResult);
    }

    public function testSuccessReadNextWithIndexName(): void
    {
        $getResult =
            fn(IEloquentBuilder $handler, array $expected): Collection => $handler
                ->readNext($this->faker->word)
                ->limit(count($expected))
                ->get();

        $this->baseRead($getResult);
    }

    public function testSuccessReadNextWithoutIndexName(): void
    {
        $getResult =
            fn(IEloquentBuilder $handler, array $expected): Collection => $handler
                ->readNext()
                ->limit(count($expected))
                ->get();

        $this->baseRead($getResult);
    }

    public function testSuccessReadLast(): void
    {
        $getResult =
            fn(IEloquentBuilder $handler, array $expected): Collection => $handler
                ->readLast($this->faker->word)
                ->limit(count($expected))
                ->get();

        $this->baseRead($getResult);
    }

    public function testSuccessReadFirstWithIndexName(): void
    {
        $getResult =
            fn(IEloquentBuilder $handler, array $expected): Collection => $handler
                ->readFirst($this->faker->word)
                ->limit(count($expected))
                ->get();

        $this->baseRead($getResult);
    }

    public function testSuccessReadFirstWithoutIndexName(): void
    {
        $getResult =
            fn(IEloquentBuilder $handler, array $expected): Collection => $handler
                ->readFirst()
                ->limit(count($expected))
                ->get();

        $this->baseRead($getResult);
    }
}
