<?php

namespace App\Security\Voter\Content;

use App\Entity\Auth\User as UserResource;
use App\Enum\RoleEnum;
use App\Dto\Schema\SchemaPutInput;
use App\Entity\Content\Schema as SchemaEntity;
use App\ApiResource\Content\Schema as SchemaResource;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class SchemaVoter extends Voter
{
  public function __construct()
  {
  }

  protected function supports(string $attribute, mixed $subject): bool
  {
    if (
      !$subject instanceof SchemaEntity &&
      !$subject instanceof SchemaPutInput &&
      !$subject instanceof SchemaResource
    ) {
      return false;
    }

    return true;
  }

  protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
  {
    $user = $subject;

    $user = $token->getUser();
    if (!$user instanceof UserResource) {
      return false;
    }

    foreach ($user->getRoles() as $role) {
      switch ($role) {
        case RoleEnum::ADMIN->value :
          if(RoleEnum::ADMIN->implies($attribute)) return true;
          break;
        case RoleEnum::MANAGER->value:
          if(RoleEnum::MANAGER->implies($attribute)) return true;
          break;
        case RoleEnum::USER->value:
          if(RoleEnum::USER->implies($attribute)) return true;
          break;
        case RoleEnum::GUEST->value:
          if(RoleEnum::GUEST->implies($attribute)) return true;
          break;
      };
    }
    return false;
  }
}
