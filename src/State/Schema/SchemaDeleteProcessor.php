<?php
namespace App\State\Schema;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\ApiResource\Content\Schema as SchemaResource;
use App\Entity\Auth\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Content\Schema as SchemaEntity;

final class SchemaDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $em,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        \assert($data instanceof SchemaResource);

        $user = $this->security->getUser();
        if(!$user || !$user instanceof User) {
            throw new InvalidArgumentException('schema.error.user.not_found');
        }

        $schema = $this->em->find(SchemaEntity::class, $uriVariables['id']);
        if (!$schema) {
            throw new NotFoundHttpException('schema.error.not_found');
        }
        if(! $this->security->isGranted('manage', $schema)) {
            throw new AccessDeniedException('schema.error.access');
        }

        $schema->setDeletedAt(new \DateTime());
        $this->em->persist($schema);
        $this->em->flush();
    }
}