<?php
/**
 * ----------------------------------------------------------------------------
 * This code is part of an application or library developed by Datamedrix and
 * is subject to the provisions of your License Agreement with
 * Datamedrix GmbH.
 *
 * @copyright (c) 2018 Datamedrix GmbH
 * ----------------------------------------------------------------------------
 * @author Christian Graf <c.graf@datamedrix.com>
 */

declare(strict_types=1);

namespace Fox\Artisan\Tests\Unit;

use Illuminate\Console\Command;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

abstract class AbstractCommandTestCase extends TestCase
{
    /**
     * @var Command|MockObject
     */
    protected $cmd;

    /**
     * @var string
     */
    protected $cmdClassName = Command::class;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->cmd = $this->getMockBuilder($this->cmdClassName)
            ->disableOriginalConstructor()
            ->setMethods(['argument', 'option', 'call', 'info', 'comment', 'error', 'warn'])
            ->getMock()
        ;
    }
}
