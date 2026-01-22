<?php

namespace App\Entity\Auth;

use Doctrine\ORM\Mapping as ORM;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken as BaseRefreshToken;

#[ORM\Entity]
#[ORM\Table(name: 'refresh_tokens')]
class RefreshToken extends BaseRefreshToken
{
    #[ORM\Column(nullable: true)]
    protected ?bool $remember = false;

    public function getRemember(): ?bool
    {
        return $this->remember;
    }

    public function setRemember(bool $remember): static
    {
        $this->remember = $remember;

        return $this;
    }
}
