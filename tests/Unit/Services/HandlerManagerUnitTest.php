<?php

declare(strict_types=1);

namespace DDA58\LaravelMySQLHandler\Tests\Unit\Services;

use DDA58\LaravelMySQLHandler\Services\Manager\HandlerManager;
use DDA58\LaravelMySQLHandler\Services\Manager\IHandlerManager;
use DDA58\LaravelMySQLHandler\Tests\ABaseTestCase;

class HandlerManagerUnitTest extends ABaseTestCase
{
    public function testSuccessInstanceOf(): void
    {
        $this->assertInstanceOf(
            IHandlerManager::class,
            $this->createMock(HandlerManager::class)
        );
    }
}
