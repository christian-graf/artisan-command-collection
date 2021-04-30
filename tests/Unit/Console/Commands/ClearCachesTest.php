<?php

declare(strict_types=1);

namespace Fox\Artisan\Tests\Unit\Console\Commands;

use Fox\Artisan\Console\Commands\ClearCaches;
use Fox\Artisan\Tests\Unit\AbstractCommandTestCase;

class ClearCachesTest extends AbstractCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected $cmdClassName = ClearCaches::class;

    /**
     * Get a list of caches which the command should be could handle!
     */
    public function getCachesToClear(): array
    {
        return [
            ['application', 'clear application cache', 'cache:clear'],
            ['app', 'clear application cache', 'cache:clear'],
            ['config', 'clear config cache', 'config:clear'],
            ['route', 'clear route cache', 'route:clear'],
            ['view', 'clear view cache', 'view:clear'],
        ];
    }

    /**
     * Test.
     */
    public function testConstructor()
    {
        $cmd = new ClearCaches();

        $this->assertAttributeContains('fox:cache:clear', 'signature', $cmd);
        $this->assertAttributeContains('{cache?*', 'signature', $cmd);
        $this->assertAttributeContains('{--all', 'signature', $cmd);

        $this->assertAttributeEquals(['app', 'config', 'route', 'view'], 'availableCacheTypes', $cmd);
    }

    /**
     * Test.
     */
    public function testHandleShowWarningIfNoCacheIsSet()
    {
        $this->cmd
            ->expects($this->once())
            ->method('argument')
            ->with('cache')
            ->willReturn(null)
        ;

        $this->cmd
            ->expects($this->once())
            ->method('warn')
            ->with('No cache type defined!')
        ;

        $this->cmd
            ->expects($this->once())
            ->method('option')
            ->with('all')
            ->willReturn(false)
        ;

        $this->assertEquals(0, $this->cmd->handle());
    }

    /**
     * Test.
     *
     * @dataProvider getCachesToClear
     */
    public function testHandle(string $cache, string $comment, string $call)
    {
        $this->cmd
            ->expects($this->once())
            ->method('argument')
            ->with('cache')
            ->willReturn([$cache])
        ;

        $this->cmd
            ->expects($this->never())
            ->method('warn')
        ;

        $this->cmd
            ->expects($this->once())
            ->method('option')
            ->with('all')
            ->willReturn(false)
        ;

        $this->cmd
            ->expects($this->once())
            ->method('comment')
            ->with($comment)
        ;

        $this->cmd
            ->expects($this->once())
            ->method('call')
            ->with($call)
        ;

        $this->assertEquals(0, $this->cmd->handle());
    }

    /**
     * Test.
     */
    public function testHandleWithOption()
    {
        $this->cmd
            ->expects($this->once())
            ->method('argument')
            ->with('cache')
            ->willReturn([])
        ;

        $this->cmd
            ->expects($this->never())
            ->method('warn')
        ;

        $this->cmd
            ->expects($this->once())
            ->method('option')
            ->with('all')
            ->willReturn(true)
        ;

        $this->cmd
            ->expects($this->exactly(4))
            ->method('comment')
            ->withConsecutive(
                ['clear application cache'],
                ['clear config cache'],
                ['clear route cache'],
                ['clear view cache']
            )
        ;

        $this->cmd
            ->expects($this->exactly(4))
            ->method('call')
            ->withConsecutive(
                ['cache:clear'],
                ['config:clear'],
                ['route:clear'],
                ['view:clear']
            )
        ;

        $this->assertEquals(0, $this->cmd->handle());
    }

    /**
     * Test.
     */
    public function testHandleReturnsError()
    {
        $unknownCache = 'unknown' . rand(10, 99);
        $this->cmd
            ->expects($this->once())
            ->method('argument')
            ->with('cache')
            ->willReturn([$unknownCache])
        ;

        $this->cmd
            ->expects($this->never())
            ->method('warn')
        ;

        $this->cmd
            ->expects($this->once())
            ->method('option')
            ->with('all')
            ->willReturn(false)
        ;

        $this->cmd
            ->expects($this->never())
            ->method('comment')
        ;

        $this->cmd
            ->expects($this->never())
            ->method('call')
        ;

        $this->cmd
            ->expects($this->once())
            ->method('error')
            ->with('unknown cache type "' . $unknownCache . '"')
        ;

        $this->assertEquals(1, $this->cmd->handle());
    }
}
