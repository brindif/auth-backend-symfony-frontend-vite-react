<?php
namespace App\Dto\Page;

use Symfony\Component\Validator\Constraints as Assert;

final class TabPostInput
{
    #[Assert\Length(max: 50, maxMessage: 'tab.post.error.name.length')]
    #[Assert\NotBlank(message: 'tab.post.error.name.empty')]
    public ?string $name = null;
    
    #[Assert\Length(max: 50, maxMessage: 'tab.post.error.name.default.length')]
    public ?string $nameDefault = null;

    #[Assert\NotBlank(message: 'tab.post.error.route.empty')]
    #[Assert\Length(max: 50, maxMessage: 'tab.post.error.route.length')]
    public ?string $route = null;

    #[Assert\PositiveOrZero(message: 'tab.post.error.position.invalid')]
    public ?int $position = null;

    public ?self $parent = null;
}
