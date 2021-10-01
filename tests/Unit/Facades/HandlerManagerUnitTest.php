<?php

declare(strict_types=1);

namespace DDA58\MySQLHandlerForLaravelQueryBuilder\Tests\Unit\Facades;

use DDA58\MySQLHandlerForLaravelQueryBuilder\Facades\HandlerManager;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Services\Manager\IHandlerManager;
use DDA58\MySQLHandlerForLaravelQueryBuilder\Tests\ABaseTestCase;

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
