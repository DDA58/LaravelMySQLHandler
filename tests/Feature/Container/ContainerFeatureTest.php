<?php

declare(strict_types=1);

namespace DDA58\MySQLHandlerForLaravelQueryBuilder\Tests\Feature\Container;

use DDA58\MySQLHandlerForLaravelQueryBuilder\Container\HandlerContainer;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Container\IHandlerContainer;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Exceptions\HandlerBuilderNotFound;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Services\Builder\Interfaces\IBuilder;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Tests\ABaseTestCase;
use Generator;
use Illuminate\Database\Query\Builder as DbQueryBuilder;

class ContainerFeatureTest extends ABaseTestCase
{
    private IHandlerContainer $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = $this->app->get(IHandlerContainer::class);
    }

    public function testSuccessInstanceOf(): void
    {
        $this->assertInstanceOf(HandlerContainer::class, $this->container);
    }

    public function handlerBuilderProvider(): Generator
    {
        if (!$this->app) {
            $this->refreshApplication();
        }

        foreach ($this->app->tagged('mysql.handler.builder') as $handlerBuilder) {
            yield [$handlerBuilder];
        }
    }

    /**
     * @dataProvider handlerBuilderProvider
     */
    public function testSuccessAddToContainer(IBuilder $handlerBuilder): void
    {
        $dbQueryBuilder = $this->app->get(DbQueryBuilder::class);

        $this->container->add(
            $dbQueryBuilder,
            $handlerBuilder
        );

        $this->assertCount(
            1,
            $this->container->getDbBuildersToOpenedHandlers()
        );
        $this->assertSame(
            $dbQueryBuilder,
            $this->container->getDbBuildersToOpenedHandlers()->current()
        );
        $this->assertSame(
            $handlerBuilder,
            $this->container->getDbBuildersToOpenedHandlers()->getInfo()
        );
    }

    /**
     * @dataProvider handlerBuilderProvider
     */
    public function testSuccessContainerContainHandlerName(IBuilder $handlerBuilder): void
    {
        $handlerName = $this->faker->word;
        $dbQueryBuilder = $this->app->get(DbQueryBuilder::class);
        $handlerBuilder->setHandlerName($handlerName);

        $this->container->add(
            $dbQueryBuilder,
            $handlerBuilder
        );

        $this->assertTrue(
            $this->container->containHandlerName($handlerName)
        );
    }

    public function testSuccessContainerNotContainHandlerName(): void
    {
        $this->assertFalse(
            $this->container->containHandlerName($this->faker->word)
        );
    }

    /**
     * @dataProvider handlerBuilderProvider
     */
    public function testSuccessContainerGetHandlerByName(IBuilder $handlerBuilder): void
    {
        $handlerName = $this->faker->word;
        $dbQueryBuilder = $this->app->get(DbQueryBuilder::class);
        $handlerBuilder->setHandlerName($handlerName);

        $this->container->add(
            $dbQueryBuilder,
            $handlerBuilder
        );

        $this->assertSame(
            $handlerBuilder,
            $this->container->getHandlerByName($handlerName)
        );
    }

    public function testFailContainerGetHandlerByName(): void
    {
        $this->expectException(HandlerBuilderNotFound::class);

        $this->container->getHandlerByName($this->faker->word);
    }

    /**
     * @dataProvider handlerBuilderProvider
     */
    public function testSuccessContainerRemove(IBuilder $handlerBuilder): void
    {
        $dbQueryBuilder = $this->app->get(DbQueryBuilder::class);

        $this->container
            ->add(
                $dbQueryBuilder,
                $handlerBuilder
            )
            ->remove($dbQueryBuilder);

        $this->assertNotContainsEquals(
            $dbQueryBuilder,
            $this->container->getDbBuildersToOpenedHandlers()
        );
    }
}
