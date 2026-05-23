<?php

namespace App\Services\Payment;

class PaymentGatewayManager
{
    public function gatewayForMode(?string $mode): PaymentGatewayInterface
    {
        $normalized = $this->normalize((string) $mode);

        if (
            str_contains($normalized, 'cash')
            || str_contains($normalized, 'comptant')
            || str_contains($normalized, 'espece')
        ) {
            return new CashPaymentGateway();
        }

        return new ExternalPaymentGateway();
    }

    private function normalize(string $value): string
    {
        $value = strtolower(trim($value));
        $value = strtr($value, [
            'é' => 'e',
            'è' => 'e',
            'ê' => 'e',
            'à' => 'a',
            'ù' => 'u',
            'ç' => 'c',
        ]);

        return $value;
    }
}
