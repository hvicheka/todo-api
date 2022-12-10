<?php

namespace App\Enums;

enum TaskStatus: string
{
    case TODO = 'todo';
    case PROGRESSING = 'progressing';
    case COMPLETED = 'completed';
}
