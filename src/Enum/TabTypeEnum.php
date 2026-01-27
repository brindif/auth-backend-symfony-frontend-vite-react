<?php

namespace App\Enum;

enum TabTypeEnum: string
{
    case NOTES = 'notes';
    case CALENDAR = 'calendar';
    case TREE = 'tree';
}