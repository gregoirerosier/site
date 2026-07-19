<?php
declare(strict_types=1);

interface NarrationProvider
{
    public function name(): string;

    public function generate(array $request): array;

    public function voices(string $language = ''): array;
}

final class NarrationProviderException extends RuntimeException
{
    /** @var string */
    private $errorCode;

    /** @var bool */
    private $fallbackAllowed;

    public function __construct(
        string $message,
        string $errorCode,
        bool $fallbackAllowed = false,
        int $statusCode = 0,
        ?Throwable $previous = null
    ) {
        $this->errorCode = $errorCode;
        $this->fallbackAllowed = $fallbackAllowed;
        parent::__construct($message, $statusCode, $previous);
    }

    public function errorCode(): string
    {
        return $this->errorCode;
    }

    public function allowsFallback(): bool
    {
        return $this->fallbackAllowed;
    }
}
