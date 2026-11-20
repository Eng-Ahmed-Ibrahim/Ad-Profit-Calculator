<?php

namespace App\Enums;

enum BottleneckResponse: string
{
    case BOTTLENECK_ADS = 'Ads / offer problem';
    case BOTTLENECK_FOLLOW_UP = 'Follow-up problem';
    case BOTTLENECK_SALES = 'Sales conversion problem';
    case BOTTLENECK_NONE = 'No major bottleneck, ready to scale carefully';
}
