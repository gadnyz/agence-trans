<?php

namespace App\Services\Payment;

interface PaymentGatewayInterface
{
    /**
     * @param array<string, mixed> $context
     * @param array<string, mixed> $payload
     */
    public function confirm(array $context, array $payload = []): PaymentResult;
}
