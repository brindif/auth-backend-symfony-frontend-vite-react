<?php

namespace App\Enum;

enum RoleEnum: string
{
  case GUEST = 'ROLE_GUEST';
  case USER = 'ROLE_USER';
  case MANAGER = 'ROLE_MANAGER';
  case ADMIN = 'ROLE_ADMIN';

  public function implies(string $needed): bool
  {
    return match ($needed) {
      self::GUEST->value => in_array($this, [self::GUEST, self::USER, self::MANAGER, self::ADMIN]),
      self::USER->value => in_array($this, [self::USER, self::MANAGER, self::ADMIN]),
      self::MANAGER->value => in_array($this, [self::MANAGER, self::ADMIN]),
      self::ADMIN->value => in_array($this, [self::ADMIN]),
    };
  }
}