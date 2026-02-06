<?php

namespace App\Security\Voter\Page;

use App\Dto\Page\TabPutInput;
use App\Entity\Page\Tab as TabEntity;
use App\ApiResource\Page\Tab as TabResource;
use App\Entity\Auth\User;
use App\Repository\Page\PermissionRepository;
use App\Enum\PermissionEnum;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class TabVoter extends Voter
{
    public function __construct(private readonly PermissionRepository $permissionRepository)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (
            !$subject instanceof TabEntity &&
            !$subject instanceof TabPutInput &&
            !$subject instanceof TabResource
        ) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        if ($subject instanceof TabEntity) {
            $tabId = $subject->getId();
        } else {
            $tabId = $subject->id;
        }

        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        $permission = $this->permissionRepository->findOneForUserAndTabId($tabId, $user);
        if (null === $permission) {
            return false;
        }

        $level = $permission->getPermission();

        return match ($attribute) {
            PermissionEnum::READ->value => $level->implies(PermissionEnum::READ),
            PermissionEnum::WRITE->value => $level->implies(PermissionEnum::WRITE),
            PermissionEnum::MANAGE->value => $level->implies(PermissionEnum::MANAGE),
            default => false,
        };
    }
}
