<?php

declare(strict_types=1);

namespace Src\Exception;

use GraphQL\Error\ClientAware;

class ClientSafeException extends \Exception implements ClientAware
{
    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return 'user';
    }
}