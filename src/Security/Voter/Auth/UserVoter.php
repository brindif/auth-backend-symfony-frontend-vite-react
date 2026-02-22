<?php

namespace App\Security\Voter\Auth;

use App\Entity\Auth\User as UserResource;
use App\Entity\Auth\User as UserEntity;
use App\Repository\Page\PermissionRepository;
use App\Enum\PermissionEnum;
use App\Enum\RoleEnum;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class UserVoter extends Voter
{
    public function __construct(private readonly PermissionRepository $permissionRepository)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!$subject instanceof UserResource) {
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
            switch ($attribute) {
                case RoleEnum::ADMIN->value :
                    if($role === RoleEnum::ADMIN->value) return true;
                    break;
                case RoleEnum::MANAGER->value:
                    if($role === RoleEnum::MANAGER->value) return true;
                    break;
                case RoleEnum::USER->value:
                    if($role === RoleEnum::USER->value) return true;
                    break;
                case RoleEnum::GUEST->value:
                    if($role === RoleEnum::GUEST->value) return true;
                    break;
            };
        }
        return false;
    }
}
