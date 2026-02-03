<?php

namespace App\Entity\Page;

use App\Entity\Auth\User;
use App\Enum\PermissionEnum;
use App\Repository\Page\PermissionRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Blameable\Traits\BlameableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: PermissionRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_PERMISSION_TAB_USER', fields: ['tab', 'user'])]
#[UniqueEntity(fields: ['email'], message: 'permission.error.permission.exists')]
#[ORM\Index(columns: ['tab_id', 'user_id'])]
class Permission
{
    use TimestampableEntity;
    use BlameableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'permissions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tab $tab = null;

    #[ORM\ManyToOne(inversedBy: 'permissions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(enumType: PermissionEnum::class)]
    private PermissionEnum $permission = PermissionEnum::READ;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTab(): ?Tab
    {
        return $this->tab;
    }

    public function setTab(?Tab $tab): static
    {
        $this->tab = $tab;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getPermission(): PermissionEnum
    {
        return $this->permission;
    }

    public function setPermission(PermissionEnum $permission): static
    {
        $this->permission = $permission;

        return $this;
    }
}
