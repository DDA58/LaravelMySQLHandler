<?php

declare(strict_types=1);

namespace DDA58\LaravelMySQLHandler\Tests\Unit\Facades;

use DDA58\LaravelMySQLHandler\Facades\HandlerManager;
use DDA58\LaravelMySQLHandler\Services\Manager\IHandlerManager;
use DDA58\LaravelMySQLHandler\Tests\ABaseTestCase;

class HandlerManagerUnitTest extends ABaseTestCase
{
    public function testSuccessInstanceOf(): void
    {
        $this->assertInstanceOf(
            IHandlerManager::class,
            HandlerManager::getFacadeRoot()
        );
    }
}
