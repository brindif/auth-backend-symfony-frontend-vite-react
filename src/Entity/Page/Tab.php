<?php

namespace App\Entity\Page;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\MenuEnum;
use App\Repository\Page\TabRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Trait\AuditableTrait;

#[ORM\Entity(repositoryClass: TabRepository::class)]
class Tab
{
    use AuditableTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameDefault = null;

    #[ORM\Column(length: 255)]
    private ?string $route = null;

    #[ORM\Column(nullable: true)]
    private ?int $position = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    private ?self $parent = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent')]
    private Collection $children;

    #[ORM\Column(enumType: MenuEnum::class)]
    private ?MenuEnum $menu = null;

    /**
     * @var Collection<int, TabPermission>
     */
    #[ORM\OneToMany(targetEntity: TabPermission::class, mappedBy: 'tab')]
    private Collection $permissions;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->permissions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getNameDefault(): ?string
    {
        return $this->nameDefault;
    }

    public function setNameDefault(?string $nameDefault): static
    {
        $this->nameDefault = $nameDefault;

        return $this;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function setRoute(string $route): static
    {
        $this->route = $route;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): static
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): static
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function getMenu(): ?MenuEnum
    {
        return $this->menu;
    }

    public function setMenu(MenuEnum $menu): static
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * @return Collection<int, TabPermission>
     */
    public function getTabPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addTabPermission(TabPermission $permission): static
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions->add($permission);
            $permission->setTab($this);
        }

        return $this;
    }

    public function removeTabPermission(TabPermission $permission): static
    {
        if ($this->permissions->removeElement($permission)) {
            // set the owning side to null (unless already changed)
            if ($permission->getTab() === $this) {
                $permission->setTab(null);
            }
        }

        return $this;
    }
}
