<?php

namespace App\Entity\Enum;
enum Rolee: string
{
    case Admin = 'Admin';
    case Enseignant = 'Enseignant';
    case Parent = 'Parent';
    case User = 'User';
}