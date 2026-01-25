<?php

namespace App\Entity\Page;

use App\Entity\Auth\User;
use App\Enum\PermissionEnum;
use App\Repository\Page\TabPermissionRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Trait\AuditableTrait;

#[ORM\Entity(repositoryClass: TabPermissionRepository::class)]
class TabPermission
{
    use AuditableTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'permissions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tab $tab = null;

    #[ORM\ManyToOne(inversedBy: 'tabPermissions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $allowedUser = null;

    #[ORM\Column(nullable: true, enumType: PermissionEnum::class)]
    private ?PermissionEnum $permission = null;

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

    public function getAllowedUser(): ?User
    {
        return $this->allowedUser;
    }

    public function setAllowedUser(?User $allowedUser): static
    {
        $this->allowedUser = $allowedUser;

        return $this;
    }

    public function getPermission(): ?PermissionEnum
    {
        return $this->permission;
    }

    public function setPermission(?PermissionEnum $permission): static
    {
        $this->permission = $permission;

        return $this;
    }
}
