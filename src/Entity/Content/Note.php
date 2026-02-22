<?php

namespace App\Entity\Content;

use App\Repository\Content\NoteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Trait\AuditableTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Entity\Page\Tab;

#[ORM\Entity(repositoryClass: NoteRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt')]
class Note
{
    use AuditableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tab $tab = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameDefault = null;

    #[ORM\Column(nullable: true)]
    private ?int $position = null;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    private ?Schema $schema = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $date = null;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getSchema(): ?Schema
    {
        return $this->schema;
    }

    public function setSchema(?Schema $schema): static
    {
        $this->schema = $schema;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(?\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }
}
