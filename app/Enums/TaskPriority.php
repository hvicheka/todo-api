<?php

namespace App\Enums;

use App\Traits\EnumConcern;

enum TaskPriority: string
{
    use EnumConcern;

    case HIGH = 'high';
    case NORMAL = 'normal';
    case LOW = 'low';
}
