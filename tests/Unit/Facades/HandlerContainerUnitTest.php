<?php

declare(strict_types=1);

namespace DDA58\MySQLHandlerForLaravelQueryBuilder\Tests\Unit\Facades;

use DDA58\MySQLHandlerForLaravelQueryBuilder\Facades\HandlerContainer;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Container\IHandlerContainer;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Tests\ABaseTestCase;

class HandlerContainerUnitTest extends ABaseTestCase
{
    public function testSuccessInstanceOf(): void
    {
        $this->assertInstanceOf(
            IHandlerContainer::class,
            HandlerContainer::getFacadeRoot()
        );
    }
}
