<?php

namespace Tests\Units;

use App\Contracts\DatabaseConnectionInterface;
use App\Database\PDOConnection;
use App\Database\MySQLiConnection;
use App\Exception\MissingArgumentException;
use App\Helpers\Config;
use mysqli;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * DatabaseConnectionTest
 *
 * @author erikjohnson06
 */
class DatabaseConnectionTest extends TestCase {

    public function testThrowsMissingArgumentsExceptionWithIncorrectCredentialKeys() {

        self::expectException(MissingArgumentException::class);

        $credentials = [];

        $pdoHandler = (new PDOConnection($credentials))->connect();
    }

    public function testConnectionToDatabaseWithPdoApi() {

        $credentials = $this->getCredentials('pdo');

        $pdoHandler = (new PDOConnection($credentials))->connect();
        self::assertInstanceOf(DatabaseConnectionInterface::class, $pdoHandler);

        return $pdoHandler;
    }

    /**
     * @depends testConnectionToDatabaseWithPdoApi
     * @param DatabaseConnectionInterface $handler
     */
    public function testPdoConnectionIsValid(DatabaseConnectionInterface $handler) {
        self::assertInstanceOf(PDO::class, $handler->getConnection());
    }

    public function testConnectionToDatabaseWithMysqliApi() {

        $credentials = $this->getCredentials('mysqli');

        $pdoHandler = (new MySQLiConnection($credentials))->connect();
        self::assertInstanceOf(DatabaseConnectionInterface::class, $pdoHandler);

        return $pdoHandler;
    }

    /**
     * @depends testConnectionToDatabaseWithMysqliApi
     * @param DatabaseConnectionInterface $handler
     */
    public function testMysqliConnectionIsValid(DatabaseConnectionInterface $handler) {
        self::assertInstanceOf(mysqli::class, $handler->getConnection());
    }
    
    private function getCredentials(string $type) {

        return array_merge(
                Config::get('database', $type),
                ['db_name' => 'bug_tracker']
        );
    }
}
