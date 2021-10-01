<?php

declare(strict_types=1);

namespace DDA58\MySQLHandlerForLaravelQueryBuilder\Tests\Unit\Services;

use DDA58\MySQLHandlerForLaravelQueryBuilder\Services\Manager\HandlerManager;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Services\Manager\IHandlerManager;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Tests\ABaseTestCase;

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
