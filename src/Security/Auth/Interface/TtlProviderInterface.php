<?php
namespace App\Security\Auth\Interface;

interface TtlProviderInterface
{
    public function getTtl(): int;
}
