<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class TDSynnexApiException extends Exception
{
    protected int $statusCode;

    public function __construct(
        string $message = '',
        int $statusCode = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render($request)
    {
        return response()->json([
            'error' => 'TD SYNNEX API Error',
            'message' => $this->getMessage(),
            'status_code' => $this->statusCode ?: 500,
        ], $this->statusCode ?: 500);
    }
}
