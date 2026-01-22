<?php
namespace App\Security\Listener\Auth;

use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGeneratorInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RequestStack;
use Gesdinet\JWTRefreshTokenBundle\Request\Extractor\ExtractorInterface;

final class AttachRefreshTokenOnSuccessListener
{
    public function __construct(
        private readonly RefreshTokenManagerInterface $refreshTokenManager,
        private readonly RefreshTokenGeneratorInterface $refreshTokenGenerator,
        private readonly RequestStack $requestStack,
        private readonly ExtractorInterface $extractor,
        private readonly array $cookieSettings,
        private readonly int $ttlShort,
        private readonly int $ttlLong,


        private readonly string $tokenParameterName,
        private readonly bool $singleUse,
        private readonly bool $returnExpiration = false,
        private readonly string $returnExpirationParameterName = 'refresh_token_expiration'
    ) {
    }

    public function attachRefreshToken(AuthenticationSuccessEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return;
        }

        $payload = json_decode($request?->getContent() ?? '', true) ?? [];
        $remember = !empty($payload['remember']);

        $user = $event->getUser();
        $data = $event->getData();

        // Extract refreshToken from the request
        $refreshTokenString = $this->extractor->getRefreshToken($request, $this->tokenParameterName);

        // Get refresh token if exists
        if ($refreshTokenString) {
            $refreshToken = $this->refreshTokenManager->get($refreshTokenString);
            // Get refresh token remember
            $remember = $refreshToken->getRemember();
            // Remove the current refreshToken if it is single-use
            if (true === $this->singleUse) {
                $refreshTokenString = null;

                if ($refreshToken instanceof RefreshTokenInterface) {
                    $this->refreshTokenManager->delete($refreshToken);
                }
            }
        }

        $ttl = $remember ? $this->ttlLong : $this->ttlShort;

        // Set or create the refreshTokenString
        if (null !== $refreshTokenString && '' !== $refreshTokenString && '0' !== $refreshTokenString) {
            $data[$this->tokenParameterName] = $refreshTokenString;

            if ($this->returnExpiration) {
                $refreshToken = $this->refreshTokenManager->get($refreshTokenString);
                $data[$this->returnExpirationParameterName] = ($refreshToken instanceof RefreshTokenInterface) ? $refreshToken->getValid()->getTimestamp() : 0;
            }
        } else {
            $refreshToken = $this->refreshTokenGenerator->createForUserWithTtl($user, $ttl);
            // Set remember in the new token
            $refreshToken->setRemember($remember);

            $this->refreshTokenManager->save($refreshToken);
            $refreshTokenString = $refreshToken->getRefreshToken();
            $data[$this->tokenParameterName] = $refreshTokenString;

            if ($this->returnExpiration) {
                $data[$this->returnExpirationParameterName] = $refreshToken->getValid()->getTimestamp();
            }
        }

        // Add a response cookie if enabled
        if ($this->cookieSettings['enabled']) {
            $event->getResponse()->headers->setCookie(
                new Cookie(
                    $this->tokenParameterName,
                    $refreshTokenString,
                    time() + $ttl,
                    $this->cookieSettings['path'] ?? '/',
                    $this->cookieSettings['domain'] ?? null,
                    $this->cookieSettings['secure'] ?? true,
                    $this->cookieSettings['http_only'] ?? true,
                    false,
                    $this->cookieSettings['same_site'] ?? 'lax',
                    $this->cookieSettings['partitioned'] ?? false,
                )
            );

            // Remove the refreshTokenString from the response body
            if (isset($this->cookieSettings['remove_token_from_body']) && $this->cookieSettings['remove_token_from_body']) {
                unset($data[$this->tokenParameterName]);
            }
        }

        // Set response data
        $event->setData($data);
    }
}
