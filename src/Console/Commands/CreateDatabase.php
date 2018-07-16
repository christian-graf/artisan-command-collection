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

namespace Fox\Artisan\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;

class CreateDatabase extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $description =
        'Create a database using the designated connection.';

    /**
     * {@inheritdoc}
     */
    protected $signature =
        'db:create 
        {connection=default : Name of the connection defined in your config/database.php file.}
        {--incl-drop-database|drop : Include drop database statement}
    ';

    /**
     * @var DatabaseManager
     */
    private $dbm;

    /**
     * CreateDatabase constructor.
     *
     * @param DatabaseManager $dbm
     */
    public function __construct(DatabaseManager $dbm)
    {
        parent::__construct();
        $this->dbm = $dbm;
    }

    /**
     * @return int
     */
    public function handle(): int
    {
        $connectionName = $this->argument('connection');
        $dropDatabase = $this->hasOption('incl-drop-database') || $this->hasOption('drop');

        if (strtolower($connectionName) === 'default') {
            $connectionName = null;
        }

        try {
            $connection = $this->dbm->connection($connectionName);
        } catch (\InvalidArgumentException $exception) {
            $this->error($exception->getMessage());

            return 1;
        }

        $driverName = strtolower($connection->getDriverName());
        $databaseName = $this->quoteIdentifier($connection->getDatabaseName(), $driverName);
        $pdo = $this->getPDO($connection);

        if ($dropDatabase) {
            if ($pdo->exec($this->getDropDatabaseSQL($databaseName, $driverName)) === false) {
                $this->error(print_r($pdo->errorInfo(), true));

                return 2;
            }
            $this->info('Database ' . $databaseName . ' successfully dropped.');
        }

        $count = $pdo->exec(
            $this->getCreateDatabaseSQL(
                $databaseName,
                $driverName,
                $connection->getConfig('charset'),
                $connection->getConfig('collation')
            )
        );

        if ($count === false) {
            $this->error(print_r($pdo->errorInfo(), true));

            return 3;
        }
        $this->info('Database ' . $databaseName . ' successfully created.');

        return 0;
    }

    /**
     * @param string $identifier
     * @param string $driverName
     *
     * @return string
     */
    private function quoteIdentifier(string $identifier, string $driverName = 'mysql'): string
    {
        switch ($driverName) {
            case 'mysql':
                return '`' . $identifier . '`';
                break;
            case 'pgsql':
                return '"' . $identifier . '"';
                break;
            case 'sqlsrv':
                return '[' . $identifier . ']';
                break;
            default:
                return $identifier;
                break;
        }
    }

    /**
     * @param Connection $connection
     *
     * @return \PDO
     */
    protected function getPDO(Connection $connection): \PDO
    {
        $driverName = $connection->getDriverName();
        $host = $connection->getConfig('host');
        $port = $connection->getConfig('port');
        $username = $connection->getConfig('username');
        $password = $connection->getConfig('password');

        if ($driverName == 'sqlsrv') {
            $dsn = $driverName . ':Server=' . $host . ',' . $port;
        } else {
            $dsn = $driverName . ':host=' . $host . ';port=' . $port;
        }

        return new \PDO($dsn, $username, $password);
    }

    /**
     * Get the sql statement to drop the designated database.
     *
     * @param string $databaseName
     * @param string $driverName
     *
     * @return string
     */
    private function getDropDatabaseSQL(string $databaseName, string $driverName = 'mysql'): string
    {
        switch ($driverName) {
            case 'mysql':
            case 'pgsql':
                return 'DROP DATABASE IF EXISTS ' . $databaseName . ';';
                break;
            default:
                return 'DROP DATABASE ' . $databaseName . ';';
                break;
        }
    }

    /**
     * @param string      $databaseName
     * @param string      $driverName
     * @param null|string $charset
     * @param null|string $collation
     *
     * @return string
     */
    private function getCreateDatabaseSQL(string $databaseName, string $driverName = 'mysql', ?string $charset = null, ?string $collation = null): string
    {
        $sql = 'CREATE DATABASE ' . $databaseName;

        if (!empty($charset) && $driverName == 'mysql') {
            $sql .= ' CHARACTER SET ' . $charset;
        }
        if (!empty($collation) && in_array($driverName, ['mysql', 'pgsql'])) {
            $sql .= ' COLLATE ' . $collation;
        }

        return $sql . ';';
    }
}
