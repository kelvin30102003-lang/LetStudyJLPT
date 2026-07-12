<?php

namespace App\Shared\Enums;

enum AttemptStatus: string
{
    case Started = 'started';
    case Paused = 'paused';
    case Submitted = 'submitted';
    case Scoring = 'scoring';
    case Scored = 'scored';
    case Expired = 'expired';
    case Cancelled = 'cancelled';
}
