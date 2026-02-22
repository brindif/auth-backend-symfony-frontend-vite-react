<?php
namespace App\State\Schema;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Schema\SchemaPutInput;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Metadata\IriConverterInterface;
use App\Entity\Page\Tab as TabEntity;
use App\Entity\Content\Schema as SchemaEntity;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use App\ApiResource\Content\Schema as SchemaResource;
use App\Repository\Page\PermissionRepository;
use App\Repository\Auth\UserRepository;
use App\Entity\Auth\User;

final class SchemaPutProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $em,
        private IriConverterInterface $iriConverter,
        private ObjectMapperInterface $objectMapper,
        private PermissionRepository $permissionRepository,
        private UserRepository $userRepository,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): SchemaResource
    {
        \assert($data instanceof SchemaPutInput);

        $user = $this->security->getUser();
        if(!$user || !$user instanceof User) {
            throw new \InvalidArgumentException('schema.error.user.not_found');
        }

        $schema = $this->em->find(SchemaEntity::class, $uriVariables['id']);
        if (!$schema) {
            throw new \InvalidArgumentException('schema.error.not_found');
        }

        $tabEntity = null;
        if ($data->tab){
            $tabResource = $this->iriConverter->getResourceFromIri($data->tab);
            $tabEntity = $this->em->getRepository(TabEntity::class)->find($tabResource->id);
            if (!$tabEntity) {
                throw new \InvalidArgumentException('schema.error.tab.not_found');
            }
        }

        $schema->setName($data->name);
        if($data->nameDefault) $schema->setNameDefault($data->nameDefault);
        //$schema->setTab($tabEntity);

        $this->em->persist($schema);
        $this->em->flush();

        $output = $this->objectMapper->map($schema, SchemaResource::class);

        return $output;
    }
}