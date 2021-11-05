<?php

declare(strict_types=1);

namespace DDA58\LaravelMySQLHandler\Tests\Feature\Services;

use Closure;
use DDA58\LaravelMySQLHandler\Services\Builder\Interfaces\IQueryBuilder;
use DDA58\LaravelMySQLHandler\Tests\ABaseTestCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

class QueryBuilderFeatureTest extends ABaseTestCase
{
    private string $tableName;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tableName = $this->faker->word;
        $this->mockedPdoStatement
            ->method('execute')
            ->withAnyParameters()
            ->willReturn(true);
        $this->mockedPdo
            ->method('prepare')
            ->withAnyParameters()
            ->willReturn($this->mockedPdoStatement);
    }

    public function testSuccessOpenHandler(): void
    {
        $handler = DB::table($this->tableName)->openHandler();

        $this->assertInstanceOf(IQueryBuilder::class, $handler);
    }

    public function testSuccessGetHandlerFirst(): void
    {
        DB::table($this->tableName)->openHandler();
        $handler = DB::query()->getHandler($this->tableName);

        $this->assertInstanceOf(IQueryBuilder::class, $handler);
    }

    public function testSuccessGetHandlerSecond(): void
    {
        $handler = DB::table($this->tableName)->getHandler();

        $this->assertInstanceOf(IQueryBuilder::class, $handler);
    }

    public function testSuccessGetHandlerThird(): void
    {
        $handler = DB::query()->getHandler($this->tableName);

        $this->assertInstanceOf(IQueryBuilder::class, $handler);
    }

    public function testSuccessCloseHandlerFirst(): void
    {
        /** @var IQueryBuilder $handler */
        $handler = DB::table($this->tableName)->openHandler();

        $this->assertNull($handler->close());
    }

    public function testSuccessCloseHandlerSecond(): void
    {
        DB::table($this->tableName)->openHandler();

        $this->assertNull(
            DB::table($this->tableName)->closeHandler()
        );
    }

    public function testSuccessCloseHandlerThird(): void
    {
        DB::table($this->tableName)->openHandler();

        $this->assertNull(
            DB::query()->closeHandler($this->tableName)
        );
    }

    private function getExpectedArray(): array
    {
        $expected = [];

        $getObject = function (): stdClass {
            $obj = new stdClass();
            $obj->id = $this->faker->word;
            return $obj;
        };

        for ($i = 0; $i < $this->faker->randomDigitNotZero(); $i++) {
            $expected[] = $getObject();
        }

        return $expected;
    }

    private function baseRead(Closure $getResult): void
    {
        /** @var IQueryBuilder $handler */
        $handler = DB::table($this->tableName)->openHandler();

        $expected = $this->getExpectedArray();
        $this->mockedPdoStatement
            ->expects(self::once())
            ->method('fetchAll')
            ->willReturn($expected);

        $result = $getResult($handler, $expected);

        $this->assertEquals(
            collect($expected),
            $result
        );
    }

    public function testSuccessReadPrimary(): void
    {
        $getResult =
            fn(IQueryBuilder $handler, array $expected): Collection => $handler
                ->readPrimary($this->faker->word)
                ->limit(count($expected))
                ->get();

        $this->baseRead($getResult);
    }

    public function testSuccessReadPrev(): void
    {
        $getResult =
            fn(IQueryBuilder $handler, array $expected): Collection => $handler
                ->readPrev($this->faker->word)
                ->limit(count($expected))
                ->get();

        $this->baseRead($getResult);
    }

    public function testSuccessReadNextWithIndexName(): void
    {
        $getResult =
            fn(IQueryBuilder $handler, array $expected): Collection => $handler
                ->readNext($this->faker->word)
                ->limit(count($expected))
                ->get();

        $this->baseRead($getResult);
    }

    public function testSuccessReadNextWithoutIndexName(): void
    {
        $getResult =
            fn(IQueryBuilder $handler, array $expected): Collection => $handler
                ->readNext()
                ->limit(count($expected))
                ->get();

        $this->baseRead($getResult);
    }

    public function testSuccessReadLast(): void
    {
        $getResult =
            fn(IQueryBuilder $handler, array $expected): Collection => $handler
                ->readLast($this->faker->word)
                ->limit(count($expected))
                ->get();

        $this->baseRead($getResult);
    }

    public function testSuccessReadFirstWithIndexName(): void
    {
        $getResult =
            fn(IQueryBuilder $handler, array $expected): Collection => $handler
                ->readFirst($this->faker->word)
                ->limit(count($expected))
                ->get();

        $this->baseRead($getResult);
    }

    public function testSuccessReadFirstWithoutIndexName(): void
    {
        $getResult =
            fn(IQueryBuilder $handler, array $expected): Collection => $handler
                ->readFirst()
                ->limit(count($expected))
                ->get();

        $this->baseRead($getResult);
    }
}
