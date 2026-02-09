<?php
namespace App\State\Note;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\ApiResource\Page\Note as NoteResource;
use App\Entity\Auth\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Page\Note as NoteEntity;

final class NoteDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $em,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        \assert($data instanceof NoteResource);

        $user = $this->security->getUser();
        if(!$user || !$user instanceof User) {
            throw new InvalidArgumentException('note.error.user.not_found');
        }

        $note = $this->em->find(NoteEntity::class, $uriVariables['id']);
        if (!$note) {
            throw new NotFoundHttpException('note.error.not_found');
        }
        if(! $this->security->isGranted('manage', $note)) {
            throw new AccessDeniedException('note.error.access');
        }

        $note->setDeletedAt(new \DateTime());
        $this->em->persist($note);
        $this->em->flush();
    }
}