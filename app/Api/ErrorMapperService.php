<?php

namespace App\Api;

class ErrorMapperService
{
    /**
     * @var array<string, string>
     */
    private array $messages = [
        'ERR_PAIEMENT_DEPASSE_SOLDE' => 'Le paiement depasse le solde restant.',
        'ERR_PLACES_INSUFFISANTES' => 'Places insuffisantes pour cette reservation.',
        'ERR_PROGRAMME_NON_RESERVABLE' => 'Ce programme ne peut plus recevoir de reservation.',
        'ERR_BUS_DEJA_PLANIFIE' => 'Ce bus est deja planifie sur ce creneau.',
        'ERR_CONDUCTEUR_DEJA_PLANIFIE' => 'Ce conducteur est deja planifie sur ce creneau.',
        'ERR_REDUCTION_SUPERIEURE_MONTANT' => 'La reduction depasse le montant initial.',
    ];

    public function messageFor(string $code): string
    {
        return $this->messages[$code] ?? 'Erreur systeme.';
    }
}
