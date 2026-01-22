<?php

namespace App\OpenApi\Auth;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\OpenApi;

final class LoginCheckDecorator implements OpenApiFactoryInterface
{
    public function __construct(private OpenApiFactoryInterface $decorated) {}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        $pathItem = $openApi->getPaths()->getPath('/api/login/check');
        if (null === $pathItem) {
            return $openApi;
        }

        $post = $pathItem->getPost();
        if (null === $post) {
            return $openApi;
        }

        $postWithAuthTag = $post->withTags(['Auth']);

        $openApi->getPaths()->addPath('/api/login/check', $pathItem->withPost($postWithAuthTag));

        return $openApi;
    }
}
