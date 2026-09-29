<?php

namespace App\Services\Mela\Exceptions;

use RuntimeException;

class MelaAiException extends RuntimeException
{
    public const NOT_CONFIGURED = 'not_configured';
    public const UNAVAILABLE = 'unavailable';
    public const RATE_LIMITED = 'rate_limited';
    public const TIMEOUT = 'timeout';
    public const INVALID_RESPONSE = 'invalid_response';
    public const CONTENT_FILTER = 'content_filter';

    public function __construct(public readonly string $reason, string $message = '', ?\Throwable $previous = null)
    {
        parent::__construct($message !== '' ? $message : $reason, 0, $previous);
    }
}
