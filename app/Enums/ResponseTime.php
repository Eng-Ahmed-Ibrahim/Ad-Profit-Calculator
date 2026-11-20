<?php

namespace App\Enums;

enum ResponseTime: string
{
    case Under5Min = 'under_5_min';
    case SameDay = 'same_day';
    case NextDayOrLater = 'next_day_or_later';
    
}