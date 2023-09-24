<?php

namespace App\Enums;

use App\Traits\EnumConcern;

enum TaskStatus: string
{
    use EnumConcern;

    case TODO = 'todo';
    case PROGRESSING = 'progressing';
    case COMPLETED = 'completed';
}
