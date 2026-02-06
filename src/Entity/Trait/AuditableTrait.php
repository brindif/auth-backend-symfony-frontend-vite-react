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

    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    #[Gedmo\Blameable(on: 'change', field: 'deletedAt')]
    private ?string $deletedBy = null;

    public function getDeletedBy(): ?string
    {
        return $this->deletedBy;
    }

    public function setDeletedBy(?string $deletedBy): self
    {
        $this->deletedBy = $deletedBy;
        return $this;
    }
}
