<?php

namespace App\Services\Payment;

class ExternalPaymentGateway implements PaymentGatewayInterface
{
    public function confirm(array $context, array $payload = []): PaymentResult
    {
        $status = strtolower(trim((string) ($payload['provider_status'] ?? $payload['statut'] ?? '')));
        $reference = trim((string) ($payload['provider_reference'] ?? $payload['reference_paiement'] ?? ''));

        if (! in_array($status, ['success', 'succeeded', 'valide', 'valid', 'ok'], true) || $reference === '') {
            return PaymentResult::failure('Le paiement doit etre confirme par le prestataire avant validation.');
        }

        return PaymentResult::success('Paiement confirme par le prestataire.', $reference, [
            'provider_status' => $status,
        ]);
    }
}
