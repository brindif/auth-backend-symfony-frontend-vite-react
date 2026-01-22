<?php
namespace App\Security\Auth;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Cookie\JWTCookieProvider as BaseJWTCookieProvider;
use Symfony\Component\HttpFoundation\Cookie;
use App\Security\Auth\Interface\TtlProviderInterface;

final class JWTCookieProvider
{
    public function __construct(
        private readonly BaseJWTCookieProvider $inner,
        private readonly TtlProviderInterface $ttlProvider,
    ) {}

    /**
     * On garde la signature compatible avec Lexik.
     * On ignore $expiresAt et on met notre TTL (court/long) selon la Request. [page:1]
     */
    public function createCookie(
        string $jwt,
        ?string $name = null,
        ?int $expiresAt = null,
        ?string $sameSite = null,
        ?string $path = null,
        ?string $domain = null,
        ?bool $secure = null,
        ?bool $httpOnly = null,
        array $split = []
    ): Cookie {
        $dynamicExpiresAt = time() + $this->ttlProvider->getTtl();

        return $this->inner->createCookie(
            $jwt,
            $name,
            $dynamicExpiresAt,
            $sameSite,
            $path,
            $domain,
            $secure,
            $httpOnly,
            $split
        );
    }
}
