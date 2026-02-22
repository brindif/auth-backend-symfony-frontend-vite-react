<?php

namespace App\Entity\Content;

use App\Entity\Page\Tab;
use App\Repository\Content\SchemaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Trait\AuditableTrait;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: SchemaRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt')]
class Schema
{
    use AuditableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $nameDefault = null;

    /**
     * @var Collection<int, Tab>
     */
    #[ORM\ManyToMany(targetEntity: Tab::class, inversedBy: 'schemas')]
    private Collection $tabs;

    #[ORM\Column(nullable: true)]
    private ?array $content = null;

    /**
     * @var Collection<int, Note>
     */
    #[ORM\OneToMany(targetEntity: Note::class, mappedBy: 'schema')]
    private Collection $notes;

    public function __construct()
    {
        $this->tabs = new ArrayCollection();
        $this->notes = new ArrayCollection();
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

    public function setNameDefault(string $nameDefault): static
    {
        $this->nameDefault = $nameDefault;

        return $this;
    }

    /**
     * @return Collection<int, Tab>
     */
    public function getTabs(): Collection
    {
        return $this->tabs;
    }

    public function addTab(Tab $tab): static
    {
        if (!$this->tabs->contains($tab)) {
            $this->tabs->add($tab);
        }

        return $this;
    }

    public function removeTab(Tab $tab): static
    {
        $this->tabs->removeElement($tab);

        return $this;
    }

    public function getContent(): ?array
    {
        return $this->content;
    }

    public function setContent(?array $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection<int, Note>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): static
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setSchema($this);
        }

        return $this;
    }

    public function removeNote(Note $note): static
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getSchema() === $this) {
                $note->setSchema(null);
            }
        }

        return $this;
    }
}
