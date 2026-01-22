<?php

namespace App\Security\Auth;

use App\Entity\Auth\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class UserChecker implements UserCheckerInterface
{
    public function __construct(private RequestStack $requestStack) {}

    public function checkPreAuth(UserInterface $user): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $path = $request?->getPathInfo();

        if ($path === '/api/login/check' && $user instanceof User && !$user->isVerified()) {
            throw new CustomUserMessageAccountStatusException('login.error.email.not.verified');
        }
    }

    public function checkPostAuth(UserInterface $user, ?TokenInterface $token = null): void
    {
    }
}
