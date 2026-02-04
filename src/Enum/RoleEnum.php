<?php

namespace App\Enum;

enum RoleEnum: string
{
    case USER = 'ROLE_USER';
    case MANAGER = 'ROLE_MANAGER';
    case ADMIN = 'ROLE_ADMIN';

    public function implies(self $needed): bool
    {
        return match ($needed) {
            self::USER => true,
            self::MANAGER => $this === self::MANAGER || $this === self::ADMIN,
            self::ADMIN => $this === self::ADMIN,
        };
    }
}