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

namespace Fox\Artisan\Tests\Unit\Console\Commands;

use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;
use PHPUnit\Framework\MockObject\MockObject;
use Fox\Artisan\Console\Commands\CreateDatabase;
use Fox\Artisan\Tests\Unit\AbstractCommandTestCase;

class CreateDatabaseTest extends AbstractCommandTestCase
{
    /**
     * @var CreateDatabase|MockObject
     */
    protected $cmd;

    /**
     * {@inheritdoc}
     */
    protected $cmdClassName = CreateDatabase::class;

    /**
     * @var DatabaseManager|MockObject
     */
    private $dbmMock;

    /**
     * @var \PDO|MockObject
     */
    private $pdoMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->pdoMock = $this->getMockBuilder(\PDO::class)->disableOriginalConstructor()->getMock();
        $this->dbmMock = $this->getMockBuilder(DatabaseManager::class)->disableOriginalConstructor()->getMock();

        $this->cmd = $this->getMockBuilder($this->cmdClassName)
            ->setConstructorArgs([$this->dbmMock])
            ->setMethods(['argument', 'option', 'call', 'info', 'comment', 'error', 'warn', 'hasOption', 'getPDO'])
            ->getMock()
        ;
        $this->cmd
            ->method('getPDO')
            ->willReturn($this->pdoMock)
        ;
    }

    public function getHandleTestData(): array
    {
        return [
            [
                'mysql',
                'foo.Bar1',
                '`foo.Bar1`',
                'CREATE DATABASE `foo.Bar1` CHARACTER SET utf8_test COLLATE utf8_test;',
                false,
                '',
            ],
            [
                'mysql',
                'foo.Bar2',
                '`foo.Bar2`',
                'CREATE DATABASE `foo.Bar2` CHARACTER SET utf8_test COLLATE utf8_test;',
                true,
                'DROP DATABASE IF EXISTS `foo.Bar2`;',
            ],
            [
                'pgsql',
                'foo3Bar',
                '"foo3Bar"',
                'CREATE DATABASE "foo3Bar" COLLATE utf8_test;',
                false,
                '',
            ],
            [
                'pgsql',
                'foo4Bar',
                '"foo4Bar"',
                'CREATE DATABASE "foo4Bar" COLLATE utf8_test;',
                true,
                'DROP DATABASE IF EXISTS "foo4Bar";',
            ],
            [
                'sqlsrv',
                'foo_bar_5',
                '[foo_bar_5]',
                'CREATE DATABASE [foo_bar_5];',
                false,
                '',
            ],
            [
                'sqlsrv',
                'foo_bar_6',
                '[foo_bar_6]',
                'CREATE DATABASE [foo_bar_6];',
                true,
                'DROP DATABASE [foo_bar_6];',
            ],
            [
                'unknown',
                '7foo_bar',
                '7foo_bar',
                'CREATE DATABASE 7foo_bar;',
                false,
                '',
            ],
            [
                'unknown',
                '8foo_bar',
                '8foo_bar',
                'CREATE DATABASE 8foo_bar;',
                true,
                'DROP DATABASE 8foo_bar;',
            ],
        ];
    }

    /**
     * Test.
     */
    public function testConstructor()
    {
        $cmd = new CreateDatabase($this->dbmMock);

        $this->assertAttributeContains('db:create', 'signature', $cmd);
        $this->assertAttributeContains('{connection=default', 'signature', $cmd);
        $this->assertAttributeContains('{--incl-drop-database|drop', 'signature', $cmd);

        $this->assertAttributeInstanceOf(DatabaseManager::class, 'dbm', $cmd);
        $this->assertAttributeEquals($this->dbmMock, 'dbm', $cmd);
    }

    /**
     * Test.
     */
    public function testHandleRaiseAnErrorIfTheConnectionNameCouldNotBeFound()
    {
        $this->cmd
            ->expects(self::once())
            ->method('argument')
            ->with('connection')
            ->willReturn('fooBar')
        ;

        $this->cmd
            ->expects(self::exactly(2))
            ->method('hasOption')
            ->withConsecutive(
                ['incl-drop-database'],
                ['drop']
            )
            ->willReturn(false)
        ;

        $this->dbmMock
            ->expects($this->once())
            ->method('connection')
            ->with('fooBar')
            ->willThrowException(new \InvalidArgumentException(__METHOD__))
       ;

        $this->cmd
            ->expects($this->once())
            ->method('error')
            ->with(__METHOD__)
        ;

        $this->assertEquals(1, $this->cmd->handle());
    }

    /**
     * Test.
     *
     * @dataProvider getHandleTestData
     *
     * @param string $dbDriverName
     * @param string $dbName
     * @param string $quotedDbName
     * @param string $expectedCreateSQL
     * @param bool   $withDrop
     * @param string $expectedDropSQL
     */
    public function testHandle(string $dbDriverName, string $dbName, string $quotedDbName, string $expectedCreateSQL, bool $withDrop, string $expectedDropSQL)
    {
        /** @var Connection|MockObject $connectionMock */
        $connectionMock = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();
        $this->cmd
            ->expects(self::once())
            ->method('argument')
            ->with('connection')
            ->willReturn('default')
        ;

        $this->cmd
            ->method('hasOption')
            ->willReturn($withDrop)
        ;

        $this->dbmMock
            ->expects($this->once())
            ->method('connection')
            ->with(null)
            ->willReturn($connectionMock)
        ;

        $this->cmd
            ->expects($this->never())
            ->method('error')
        ;

        $connectionMock
            ->expects($this->exactly(1))
            ->method('getDriverName')
            ->willReturn($dbDriverName)
        ;

        $connectionMock
            ->expects($this->once())
            ->method('getDatabaseName')
            ->willReturn($dbName)
        ;

        $connectionMock
            ->expects($this->exactly(2))
            ->method('getConfig')
            ->withConsecutive(
                ['charset'],
                ['collation']
            )
            ->willReturn('utf8_test');

        if ($withDrop === true) {
            $this->pdoMock
                ->expects($this->exactly(2))
                ->method('exec')
                ->withConsecutive(
                    [$expectedDropSQL],
                    [$expectedCreateSQL]
                )
            ;

            $this->cmd
                ->expects($this->exactly(2))
                ->method('info')
                ->withConsecutive(
                    ['Database ' . $quotedDbName . ' successfully dropped.'],
                    ['Database ' . $quotedDbName . ' successfully created.']
                )
            ;
        } else {
            $this->pdoMock
                ->expects($this->once())
                ->method('exec')
                ->with($expectedCreateSQL)
            ;
            $this->cmd
                ->expects($this->once())
                ->method('info')
                ->with('Database ' . $quotedDbName . ' successfully created.')
            ;
        }

        $this->assertEquals(0, $this->cmd->handle());
    }
}
