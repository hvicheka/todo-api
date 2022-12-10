<?php

namespace App\Enums;

enum TaskPriority: string
{
    case HIGH = 'high';
    case NORMAL = 'normal';
    case LOW = 'low';
}
