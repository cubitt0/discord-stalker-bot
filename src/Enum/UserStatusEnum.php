<?php

declare(strict_types=1);

namespace App\Enum;

enum UserStatusEnum: string
{
    case ONLINE    = 'online';
    case IDLE      = 'idle';
    case DND       = 'dnd';
    case INVISIBLE = 'invisible';
    case OFFLINE   = 'offline';
}
