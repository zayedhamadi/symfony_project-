<?php

namespace App\Entity\Enum;

enum EventType: string
{
    case SORTIE_SCOLAIRE = 'Sortie scolaire';
    case REUNION = 'Réunion';
    case COMPETITION = 'Compétition';
    case ATELIER = 'Atelier';
    case AUTRE = 'Autre';
}
