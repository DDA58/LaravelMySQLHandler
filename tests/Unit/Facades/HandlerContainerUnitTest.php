<?php

declare(strict_types=1);

namespace DDA58\LaravelMySQLHandler\Tests\Unit\Facades;

use DDA58\LaravelMySQLHandler\Facades\HandlerContainer;
use DDA58\LaravelMySQLHandler\Container\IHandlerContainer;
use DDA58\LaravelMySQLHandler\Tests\ABaseTestCase;

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
