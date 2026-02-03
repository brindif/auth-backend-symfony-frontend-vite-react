<?php

namespace App\Enum;

enum PermissionEnum: string
{
    case MANAGE = 'manage';
    case WRITE = 'write';
    case READ = 'read';

    public function implies(self $needed): bool
    {
        return match ($needed) {
            self::READ => true,
            self::WRITE => $this === self::WRITE || $this === self::MANAGE,
            self::MANAGE => $this === self::MANAGE,
        };
    }
}