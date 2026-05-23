<?php

namespace App\Services\Payment;

class CashPaymentGateway implements PaymentGatewayInterface
{
    public function confirm(array $context, array $payload = []): PaymentResult
    {
        $reference = $payload['reference_paiement'] ?? null;

        if (! is_string($reference) || trim($reference) === '') {
            $reference = 'CASH-' . date('YmdHis') . '-' . random_int(1000, 9999);
        }

        return PaymentResult::success('Paiement comptant confirme.', $reference);
    }
}
