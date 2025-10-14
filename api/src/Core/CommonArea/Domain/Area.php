<?php

declare(strict_types=1);

namespace App\Core\CommonArea\Domain;

enum Area: string
{
    case GYM = 'gym';
    case PADEL = 'padel';
    case POOL = 'pool';
}
