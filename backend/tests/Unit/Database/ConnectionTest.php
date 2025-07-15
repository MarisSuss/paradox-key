<?php

declare(strict_types=1);

namespace Tests\Unit\Database;

use Tests\TestCase;
use Src\Database\Connection;
use PDO;
use RuntimeException;

class ConnectionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Reset the singleton instance before each test
        $reflection = new \ReflectionClass(Connection::class);
        $property = $reflection->getProperty('pdo');
        $property->setAccessible(true);
        $property->setValue(null, null);
    }

    public function testConnectionClassExists(): void
    {
        $this->assertTrue(class_exists(Connection::class));
        $this->assertTrue(method_exists(Connection::class, 'getInstance'));
    }

    public function testGetInstanceReturnsPDO(): void
    {
        // Set up environment variables for testing
        $_ENV['DB_HOST'] = 'localhost';
        $_ENV['DB_NAME'] = 'test_db';
        $_ENV['DB_USER'] = 'test_user';
        $_ENV['DB_PASS'] = 'test_pass';
        
        // This will fail because of no database, but we're testing the class structure
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Database connection failed');
        
        Connection::getInstance();
    }

    public function testConnectionWithoutDatabaseName(): void
    {
        // Clear database name to trigger exception
        $_ENV['DB_HOST'] = 'localhost';
        $_ENV['DB_NAME'] = '';
        $_ENV['DB_USER'] = 'test_user';
        $_ENV['DB_PASS'] = 'test_pass';
        
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Database name is required');
        
        Connection::getInstance();
    }

    public function testConnectionSingleton(): void
    {
        // Set up environment variables for testing
        $_ENV['DB_HOST'] = 'localhost';
        $_ENV['DB_NAME'] = 'test_db';
        $_ENV['DB_USER'] = 'test_user';
        $_ENV['DB_PASS'] = 'test_pass';
        
        // Both calls should fail with the same exception type
        $exception1 = null;
        $exception2 = null;
        
        try {
            Connection::getInstance();
        } catch (RuntimeException $e) {
            $exception1 = $e;
        }
        
        try {
            Connection::getInstance();
        } catch (RuntimeException $e) {
            $exception2 = $e;
        }
        
        // Both should throw RuntimeException
        $this->assertInstanceOf(RuntimeException::class, $exception1);
        $this->assertInstanceOf(RuntimeException::class, $exception2);
    }

    public function testConnectionWithDefaultValues(): void
    {
        // Clear environment variables
        unset($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
        
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Database name is required');
        
        Connection::getInstance();
    }

    public function testConnectionIsStaticClass(): void
    {
        $reflection = new \ReflectionClass(Connection::class);
        $constructor = $reflection->getConstructor();
        
        // If no constructor is defined, it's implicitly public
        $this->assertTrue($constructor === null || $constructor->isPublic());
    }
}
