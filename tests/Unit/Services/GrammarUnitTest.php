<?php

declare(strict_types=1);

namespace DDA58\MySQLHandlerForLaravelQueryBuilder\Tests\Unit\Services;

use DDA58\MySQLHandlerForLaravelQueryBuilder\Exceptions\KeywordNotAllowed;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Exceptions\KeywordNotAllowedForIndexName;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Exceptions\KeywordNotAllowedWithoutIndexName;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Services\Builder\ABuilder;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Services\Grammar\Grammar;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Services\Grammar\IGrammar;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Tests\ABaseTestCase;
use Illuminate\Database\Query\Builder as DbBuilder;
use Illuminate\Database\Query\Grammars\Grammar as DbGrammar;
use PHPUnit\Framework\MockObject\MockObject;

class GrammarUnitTest extends ABaseTestCase
{
    private IGrammar $grammar;
    /** @var ABuilder&MockObject  */
    private ABuilder $handlerBuilder;
    private string $handlerName;
    private int $limit;
    private int $offset;
    private int $whereValue;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handlerBuilder = $this->createMock(ABuilder::class);
        $this->handlerName = $this->faker->word;
        $this->limit = $this->faker->randomDigitNotNull();
        $this->offset = $this->faker->randomDigitNotNull;
        $this->whereValue = $this->faker->randomDigit;
        $this->grammar = new Grammar();
    }

    public function testSuccessInstanceOf(): void
    {
        $this->assertInstanceOf(
            IGrammar::class,
            $this->grammar
        );
    }

    private function assertSuccessCompileBase(): void
    {
        $dbBuilder = $this->createMock(DbBuilder::class);
        $dbGrammar = $this->createMock(DbGrammar::class);
        $dbGrammar
            ->expects(self::once())
            ->method('compileWheres')
            ->with($dbBuilder)
            ->willReturn('WHERE `id` = ' . $this->whereValue);
        $dbBuilder
            ->method('getGrammar')
            ->willReturn($dbGrammar);

        $this->handlerBuilder
            ->expects(self::once())
            ->method('getHandlerName')
            ->willReturn($this->handlerName);
        $this->handlerBuilder
            ->expects(self::exactly(2))
            ->method('getDatabaseQueryBuilder')
            ->willReturn($dbBuilder);
        $this->handlerBuilder
            ->expects(self::exactly(2))
            ->method('getLimit')
            ->willReturn($this->limit);
        $this->handlerBuilder
            ->expects(self::exactly(2))
            ->method('getOffset')
            ->willReturn($this->offset);
    }

    private function assertFailCompileBase(): void
    {
        $this->handlerBuilder
            ->expects(self::never())
            ->method('getHandlerName');
        $this->handlerBuilder
            ->expects(self::never())
            ->method('getDatabaseQueryBuilder');
        $this->handlerBuilder
            ->expects(self::never())
            ->method('getLimit');
        $this->handlerBuilder
            ->expects(self::never())
            ->method('getOffset');
    }

    public function testSuccessCompileReadPrimaryWithKeywordOne(): void
    {
        $keyword = $this->faker->randomElement(
            $this->grammar->getAllowedComparisonSymbols()
        );
        $indexValue = $this->faker->word;

        $this->handlerBuilder
            ->expects(self::once())
            ->method('getKeyword')
            ->willReturn($keyword);
        $this->handlerBuilder
            ->method('getIndexValue')
            ->willReturn([$indexValue]);

        $this->assertSuccessCompileBase();

        $result = $this->grammar->compileReadPrimary($this->handlerBuilder);

        $this->assertEquals(
            sprintf(
                'HANDLER `%s` READ `PRIMARY` %s (?) WHERE `id` = %s LIMIT %d OFFSET %d',
                $this->handlerName,
                $keyword,
                $this->whereValue,
                $this->limit,
                $this->offset
            ),
            $result
        );
    }

    public function testSuccessCompileReadPrimaryWithKeywordTwo(): void
    {
        $keyword = $this->faker->randomElement(
            $this->grammar->getAllowedKeyWordsForIndex()
        );
        $indexValue = $this->faker->word;

        $this->handlerBuilder
            ->expects(self::once())
            ->method('getKeyword')
            ->willReturn($keyword);
        $this->handlerBuilder
            ->method('getIndexValue')
            ->willReturn([$indexValue]);

        $this->assertSuccessCompileBase();

        $result = $this->grammar->compileReadPrimary($this->handlerBuilder);

        $this->assertEquals(
            sprintf(
                'HANDLER `%s` READ `PRIMARY` %s WHERE `id` = %s LIMIT %d OFFSET %d',
                $this->handlerName,
                $keyword,
                $this->whereValue,
                $this->limit,
                $this->offset
            ),
            $result
        );
    }

    public function testFailCompileReadPrimaryWithWrongKeyword(): void
    {
        $keyword = $this->faker->word . $this->faker->randomDigit;
        $indexValue = $this->faker->word;

        $this->handlerBuilder
            ->expects(self::once())
            ->method('getKeyword')
            ->willReturn($keyword);
        $this->handlerBuilder
            ->method('getIndexValue')
            ->willReturn([$indexValue]);

        $this->assertFailCompileBase();
        $this->expectException(KeywordNotAllowed::class);
        $this->expectExceptionMessage(
            sprintf('Keyword "%s" not allowed for MySQL handler statement', $keyword)
        );

        $this->grammar->compileReadPrimary($this->handlerBuilder);
    }

    public function testSuccessCompileReadPrev(): void
    {
        $indexName = $this->faker->word;

        $this->handlerBuilder
            ->expects(self::once())
            ->method('getIndexName')
            ->willReturn($indexName);

        $this->assertSuccessCompileBase();

        $result = $this->grammar->compileReadPrev($this->handlerBuilder);

        $this->assertEquals(
            sprintf(
                'HANDLER `%s` READ `%s` PREV WHERE `id` = %s LIMIT %d OFFSET %d',
                $this->handlerName,
                $indexName,
                $this->whereValue,
                $this->limit,
                $this->offset
            ),
            $result
        );
    }

    public function testSuccessCompileReadNextWithIndexName(): void
    {
        $indexName = $this->faker->word;

        $this->handlerBuilder
            ->expects(self::exactly(2))
            ->method('getIndexName')
            ->willReturn($indexName);

        $this->assertSuccessCompileBase();

        $result = $this->grammar->compileReadNext($this->handlerBuilder);

        $this->assertEquals(
            sprintf(
                'HANDLER `%s` READ `%s` NEXT WHERE `id` = %s LIMIT %d OFFSET %d',
                $this->handlerName,
                $indexName,
                $this->whereValue,
                $this->limit,
                $this->offset
            ),
            $result
        );
    }

    public function testSuccessCompileReadNextWithoutIndexName(): void
    {
        $this->handlerBuilder
            ->expects(self::once())
            ->method('getIndexName')
            ->willReturn('');

        $this->assertSuccessCompileBase();

        $result = $this->grammar->compileReadNext($this->handlerBuilder);

        $this->assertEquals(
            sprintf(
                'HANDLER `%s` READ NEXT WHERE `id` = %s LIMIT %d OFFSET %d',
                $this->handlerName,
                $this->whereValue,
                $this->limit,
                $this->offset
            ),
            $result
        );
    }

    public function testSuccessCompileReadLast(): void
    {
        $indexName = $this->faker->word;

        $this->handlerBuilder
            ->expects(self::once())
            ->method('getIndexName')
            ->willReturn($indexName);

        $this->assertSuccessCompileBase();

        $result = $this->grammar->compileReadLast($this->handlerBuilder);

        $this->assertEquals(
            sprintf(
                'HANDLER `%s` READ `%s` LAST WHERE `id` = %s LIMIT %d OFFSET %d',
                $this->handlerName,
                $indexName,
                $this->whereValue,
                $this->limit,
                $this->offset
            ),
            $result
        );
    }

    public function testSuccessCompileReadFirstWithIndexName(): void
    {
        $indexName = $this->faker->word;

        $this->handlerBuilder
            ->expects(self::exactly(2))
            ->method('getIndexName')
            ->willReturn($indexName);

        $this->assertSuccessCompileBase();

        $result = $this->grammar->compileReadFirst($this->handlerBuilder);

        $this->assertEquals(
            sprintf(
                'HANDLER `%s` READ `%s` FIRST WHERE `id` = %s LIMIT %d OFFSET %d',
                $this->handlerName,
                $indexName,
                $this->whereValue,
                $this->limit,
                $this->offset
            ),
            $result
        );
    }

    public function testSuccessCompileReadFirstWithoutIndexName(): void
    {
        $this->handlerBuilder
            ->expects(self::once())
            ->method('getIndexName')
            ->willReturn('');

        $this->assertSuccessCompileBase();

        $result = $this->grammar->compileReadFirst($this->handlerBuilder);

        $this->assertEquals(
            sprintf(
                'HANDLER `%s` READ FIRST WHERE `id` = %s LIMIT %d OFFSET %d',
                $this->handlerName,
                $this->whereValue,
                $this->limit,
                $this->offset
            ),
            $result
        );
    }

    public function testSuccessCompileReadWithIndexNameWithKeywordOne(): void
    {
        $indexValue = $this->faker->word;
        $keyword = $this->faker->randomElement(
            $this->grammar->getAllowedComparisonSymbols()
        );
        $indexName = $this->faker->word;

        $this->handlerBuilder
            ->expects(self::once())
            ->method('getKeyword')
            ->willReturn($keyword);
        $this->handlerBuilder
            ->expects(self::once())
            ->method('getIndexValue')
            ->willReturn([$indexValue]);
        $this->handlerBuilder
            ->expects(self::once())
            ->method('getIndexName')
            ->willReturn($indexName);

        $this->assertSuccessCompileBase();

        $result = $this->grammar->compileRead($this->handlerBuilder);

        $this->assertEquals(
            sprintf(
                'HANDLER `%s` READ `%s` %s (?) WHERE `id` = %s LIMIT %d OFFSET %d',
                $this->handlerName,
                $indexName,
                $keyword,
                $this->whereValue,
                $this->limit,
                $this->offset
            ),
            $result
        );
    }

    public function testSuccessCompileReadWithIndexNameWithKeywordTwo(): void
    {
        $indexName = $this->faker->word;
        $keyword = $this->faker->randomElement(
            $this->grammar->getAllowedKeyWordsForIndex()
        );
        $indexValue = $this->faker->word;

        $this->handlerBuilder
            ->expects(self::once())
            ->method('getKeyword')
            ->willReturn($keyword);
        $this->handlerBuilder
            ->expects(self::once())
            ->method('getIndexValue')
            ->willReturn([$indexValue]);
        $this->handlerBuilder
            ->expects(self::once())
            ->method('getIndexName')
            ->willReturn($indexName);

        $this->assertSuccessCompileBase();

        $result = $this->grammar->compileRead($this->handlerBuilder);

        $this->assertEquals(
            sprintf(
                'HANDLER `%s` READ `%s` %s WHERE `id` = %s LIMIT %d OFFSET %d',
                $this->handlerName,
                $indexName,
                $keyword,
                $this->whereValue,
                $this->limit,
                $this->offset
            ),
            $result
        );
    }

    public function testFailCompileReadWithIndexNameWithWrongKeyword(): void
    {
        $keyword = $this->faker->word . $this->faker->randomDigit;
        $indexName = $this->faker->word;
        $indexValue = $this->faker->word;

        $this->handlerBuilder
            ->expects(self::once())
            ->method('getKeyword')
            ->willReturn($keyword);
        $this->handlerBuilder
            ->expects(self::once())
            ->method('getIndexValue')
            ->willReturn([$indexValue]);
        $this->handlerBuilder
            ->expects(self::once())
            ->method('getIndexName')
            ->willReturn($indexName);

        $this->assertFailCompileBase();
        $this->expectException(KeywordNotAllowedForIndexName::class);
        $this->expectExceptionMessage(
            sprintf(
                'Keyword "%s" not allowed for MySQL handler statement with index name "%s"',
                $keyword,
                $indexName
            )
        );

        $this->grammar->compileRead($this->handlerBuilder);
    }

    public function testSuccessCompileReadWithoutIndexName(): void
    {
        $keyword = $this->faker->randomElement(
            $this->grammar->getAllowedKeyWordsForHandler()
        );

        $this->handlerBuilder
            ->expects(self::once())
            ->method('getKeyword')
            ->willReturn($keyword);
        $this->handlerBuilder
            ->expects(self::once())
            ->method('getIndexValue')
            ->willReturn(['']);
        $this->handlerBuilder
            ->expects(self::once())
            ->method('getIndexName')
            ->willReturn('');

        $this->assertSuccessCompileBase();

        $result = $this->grammar->compileRead($this->handlerBuilder);

        $this->assertEquals(
            sprintf(
                'HANDLER `%s` READ %s WHERE `id` = %s LIMIT %d OFFSET %d',
                $this->handlerName,
                $keyword,
                $this->whereValue,
                $this->limit,
                $this->offset
            ),
            $result
        );
    }

    public function testFailCompileReadWithoutIndexNameWithWrongKeyword(): void
    {
        $keyword = $this->faker->word . $this->faker->randomDigit;

        $this->handlerBuilder
            ->expects(self::once())
            ->method('getKeyword')
            ->willReturn($keyword);
        $this->handlerBuilder
            ->expects(self::once())
            ->method('getIndexValue')
            ->willReturn(['']);
        $this->handlerBuilder
            ->expects(self::once())
            ->method('getIndexName')
            ->willReturn('');

        $this->assertFailCompileBase();
        $this->expectException(KeywordNotAllowedWithoutIndexName::class);
        $this->expectExceptionMessage(
            sprintf('Keyword "%s" not allowed for MySQL handler statement without index name', $keyword)
        );

        $this->grammar->compileRead($this->handlerBuilder);
    }

    public function testSuccessCompileLimit(): void
    {
        $result = $this->grammar->compileLimit($this->limit);

        $this->assertEquals(
            sprintf(
                'LIMIT %s',
                $this->limit,
            ),
            $result
        );
    }

    public function testSuccessCompileOffset(): void
    {
        $result = $this->grammar->compileOffset($this->offset);

        $this->assertEquals(
            sprintf(
                'OFFSET %s',
                $this->offset,
            ),
            $result
        );
    }

    public function testSuccessCompileOpen(): void
    {
        $tableName = $this->faker->word;

        $result = $this->grammar->compileOpen($tableName, $this->handlerName);

        $this->assertEquals(
            sprintf(
                'HANDLER `%s` OPEN AS `%s`',
                $tableName,
                $this->handlerName
            ),
            $result
        );
    }

    public function testSuccessCompileClose(): void
    {
        $result = $this->grammar->compileClose($this->handlerName);

        $this->assertEquals(
            sprintf(
                'HANDLER `%s` CLOSE',
                $this->handlerName
            ),
            $result
        );
    }
}
