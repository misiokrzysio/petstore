<?php

namespace App\Enums;

enum PetStatus: string
{
    case AVAILABLE = 'available';
    case PENDING = 'pending';
    case SOLD = 'sold';
    case UNDEFINED = 'undefined';
}
