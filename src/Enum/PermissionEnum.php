<?php

namespace App\Enum;

enum PermissionEnum: string
{
    case WRITE = 'write';
    case READ = 'read';
}