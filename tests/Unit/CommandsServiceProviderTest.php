<?php

declare(strict_types=1);

namespace Fox\Artisan\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Fox\Artisan\CommandsServiceProvider;
use PHPUnit\Framework\MockObject\MockObject;
use Fox\Artisan\Console\Commands\ClearCaches;
use Fox\Artisan\Console\Commands\CreateDatabase;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;

class CommandsServiceProviderTest extends TestCase
{
    /**
     * @var ApplicationContract|MockObject
     */
    private $appMock;

    /**
     * @var CommandsServiceProvider|MockObject
     */
    private $serviceProvider;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->appMock = $this->getMockBuilder(ApplicationContract::class)->disableOriginalConstructor()->getMock();
        $this->serviceProvider = $this->getMockBuilder(CommandsServiceProvider::class)
            ->setConstructorArgs([$this->appMock])
            ->onlyMethods(['commands'])
            ->getMock()
        ;
    }

    /**
     * Test.
     */
    public function testRegisterRunningInConsole()
    {
        $this->appMock
            ->expects($this->once())
            ->method('runningInConsole')
            ->willReturn(true)
        ;

        $this->serviceProvider
            ->expects($this->once())
            ->method('commands')
            ->with([
                ClearCaches::class,
                CreateDatabase::class,
            ])
        ;

        $this->serviceProvider->register();
    }

    /**
     * Test.
     */
    public function testRegisterRunningNotInConsole()
    {
        $this->appMock
            ->expects($this->once())
            ->method('runningInConsole')
            ->willReturn(false)
        ;

        $this->serviceProvider
            ->expects($this->never())
            ->method('commands')
        ;

        $this->serviceProvider->register();
    }

    /**
     * Test.
     */
    public function testProvides()
    {
        $this->assertEquals(['commands'], $this->serviceProvider->provides());
    }
}
