<?php

namespace App\Entity\Enum;

enum Statut: string
{
    case EN_ATTENTE = 'En attente';
    case EN_COURS = 'En cours';
    case TRAITEE = 'traitee';

    public function label(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'En attente',
            self::EN_COURS => 'En cours',
            self::TRAITEE => 'Traitée',
        };
    }
}
