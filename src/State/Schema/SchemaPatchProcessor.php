<?php
namespace App\State\Schema;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Schema\SchemaPatchInput;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Content\Schema as SchemaEntity;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use App\ApiResource\Content\Schema as SchemaResource;
use App\Entity\Auth\User;

final class SchemaPatchProcessor implements ProcessorInterface
{
  public function __construct(
    private Security $security,
    private EntityManagerInterface $em,
    private ObjectMapperInterface $objectMapper,
  ) {}

  public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): SchemaResource
  {
    \assert($data instanceof SchemaPatchInput);

    $user = $this->security->getUser();
    if(!$user || !$user instanceof User) {
      throw new \InvalidArgumentException('schema.error.user.not_found');
    }

    $schema = $this->em->find(SchemaEntity::class, $uriVariables['id']);
    if (!$schema) {
      throw new \InvalidArgumentException('schema.error.not_found');
    }

    $schema->setContent($data->content);

    $this->em->persist($schema);
    $this->em->flush();

    $output = $this->objectMapper->map($schema, SchemaResource::class);

    return $output;
  }
}