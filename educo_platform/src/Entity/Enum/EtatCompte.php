<?php

namespace App\Entity\Enum;
enum EtatCompte: string
{
    case Active = 'active';
    case Inactive = 'inactive';
}