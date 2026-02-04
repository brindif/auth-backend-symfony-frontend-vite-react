<?php

namespace App\Dto\Page;

use App\Entity\Auth\User;
use App\Enum\PermissionEnum;
use App\Entity\Page\Permission as PermissionEntity;
use Symfony\Component\ObjectMapper\Attribute\Map;
use ApiPlatform\Metadata\ApiProperty;

#[Map(source: PermissionEntity::class)]
final class Permission
{
  #[ApiProperty(readable: false, writable: true)]
  public ?User $user = null;

  public ?PermissionEnum $permission = null;
}
