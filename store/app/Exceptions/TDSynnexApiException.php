<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class TDSynnexApiException extends Exception
{
    protected int $statusCode;
    protected array $details;

    public function __construct(
        string $message = '',
        int $statusCode = 0,
        array $details = [],
        ?Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
        $this->statusCode = $statusCode;
        $this->details = $details;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getDetails(): array
    {
        return $this->details;
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
            'details' => $this->details,
        ], $this->statusCode ?: 500);
    }
}
