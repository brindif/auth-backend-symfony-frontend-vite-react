<?php
namespace App\Dto\Note;

use Symfony\Component\Validator\Constraints as Assert;

final class NotePatchInput
{
    #[Assert\Type(type: 'string', message: 'note.error.content.type')]
    public ?string $content = null;
}
