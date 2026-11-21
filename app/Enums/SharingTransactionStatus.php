<?php

namespace App\Enums;

enum SharingTransactionStatus: string
{
    case Active = 'active';
    case Completed = 'completed';
}
