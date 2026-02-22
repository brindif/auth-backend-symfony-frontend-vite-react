<?php

namespace App\Security\Voter\Content;

use App\Dto\Note\NotePutInput;
use App\Entity\Content\Note as NoteEntity;
use App\ApiResource\Content\Note as NoteResource;
use App\Entity\Auth\User;
use App\Repository\Page\PermissionRepository;
use App\Enum\PermissionEnum;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class NoteVoter extends Voter
{
    public function __construct(private readonly PermissionRepository $permissionRepository)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (
            !$subject instanceof NoteEntity &&
            !$subject instanceof NotePutInput &&
            !$subject instanceof NoteResource
        ) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        if ($subject instanceof NoteEntity) {
            $noteId = $subject->getId();
        } else {
            $noteId = $subject->id;
        }

        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        $permission = $this->permissionRepository->findOneForUserAndNoteId($noteId, $user);
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
