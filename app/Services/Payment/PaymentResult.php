<?php

namespace App\Services\Payment;

class PaymentResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly ?string $reference = null,
        public readonly array $metadata = []
    ) {
    }

    public static function success(string $message, ?string $reference = null, array $metadata = []): self
    {
        return new self(true, $message, $reference, $metadata);
    }

    public static function failure(string $message, array $metadata = []): self
    {
        return new self(false, $message, null, $metadata);
    }
}
