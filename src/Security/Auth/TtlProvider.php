<?php
namespace App\Security\Auth;

use App\Security\Auth\Interface\TtlProviderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class TtlProvider implements TtlProviderInterface
{
    public const REMEMBER = 'remember';

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly int $ttlShort,
        private readonly int $ttlLong,
    ) {}

    public function getTtl(): int
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return $this->ttlShort;
        }

        $content = (string) $request->getContent();
        if ($content === '') {
            return $this->ttlShort;
        }

        $data = json_decode($content, true);
        $remember = is_array($data) && ($data[self::REMEMBER] ?? false) === true;

        return $remember ? $this->ttlLong : $this->ttlShort;
    }
}
