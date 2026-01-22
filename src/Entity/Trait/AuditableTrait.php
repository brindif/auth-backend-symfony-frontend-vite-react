<?php

namespace App\Entity\Trait;

use App\Entity\Auth\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

trait AuditableTrait
{
    use TimestampableEntity;   // createdAt, updatedAt
    use BlameableEntity;       // createdBy, updatedBy
    use SoftDeleteableEntity;  // deletedAt

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'deleted_by', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Gedmo\Blameable(on: 'change', field: 'deletedAt')]
    private ?User $deletedBy = null;

    public function getDeletedBy(): ?User
    {
        return $this->deletedBy;
    }

    public function setDeletedBy(?User $deletedBy): self
    {
        $this->deletedBy = $deletedBy;
        return $this;
    }
}
