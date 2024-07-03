<?php

namespace App\Exception;

class OutingStatusException extends \Exception
{
    private string $flashType;

    public function __construct(string $message, string $flashType = 'error', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->flashType = $flashType;
    }

    public function getFlashType(): string
    {
        return $this->flashType;
    }

}