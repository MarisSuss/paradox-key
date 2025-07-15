<?php

declare(strict_types=1);

namespace Tests\Unit\Exception;

use Tests\TestCase;
use Src\Exception\ClientSafeException;
use GraphQL\Error\ClientAware;

class ClientSafeExceptionTest extends TestCase
{
    public function testExceptionImplementsClientAware(): void
    {
        $exception = new ClientSafeException('Test error');
        
        $this->assertInstanceOf(ClientAware::class, $exception);
    }

    public function testIsClientSafeReturnsTrue(): void
    {
        $exception = new ClientSafeException('Test error');
        
        $this->assertTrue($exception->isClientSafe());
    }

    public function testGetCategoryReturnsUser(): void
    {
        $exception = new ClientSafeException('Test error');
        
        $this->assertEquals('user', $exception->getCategory());
    }

    public function testExceptionMessage(): void
    {
        $message = 'This is a test error message';
        $exception = new ClientSafeException($message);
        
        $this->assertEquals($message, $exception->getMessage());
    }

    public function testExceptionCanBeThrown(): void
    {
        $this->expectException(ClientSafeException::class);
        $this->expectExceptionMessage('Test exception');
        
        throw new ClientSafeException('Test exception');
    }
}
