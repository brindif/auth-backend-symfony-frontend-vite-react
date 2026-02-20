<?php
namespace App\State\Auth;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\ApiResource\Auth\User as UserResource;
use App\Entity\Auth\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Auth\User as UserEntity;

final class UserDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $em,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        \assert($data instanceof UserResource);

        $user = $this->security->getUser();
        if(!$user || !$user instanceof User) {
            throw new InvalidArgumentException('user.error.user.not_found');
        }

        $user = $this->em->find(UserEntity::class, $uriVariables['id']);
        if (!$user) {
            throw new NotFoundHttpException('user.error.not_found');
        }
        if(! $this->security->isGranted('ROLE_ADMIN', $user)) {
            throw new AccessDeniedException('user.error.access');
        }

        $user->setDeletedAt(new \DateTime());
        $this->em->persist($user);
        $this->em->flush();
    }
}