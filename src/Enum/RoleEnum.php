<?php

namespace App\Enum;

enum RoleEnum: string
{
    case GUEST = 'ROLE_GUEST';
    case USER = 'ROLE_USER';
    case MANAGER = 'ROLE_MANAGER';
    case ADMIN = 'ROLE_ADMIN';

    public function implies(self $needed): bool
    {
        return match ($needed) {
            self::GUEST => true,
            self::USER => true,
            self::MANAGER => $this === self::MANAGER || $this === self::ADMIN,
            self::ADMIN => $this === self::ADMIN,
        };
    }
}