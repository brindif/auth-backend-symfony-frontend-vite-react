<?php
namespace App\State\Page;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Page\TabPostInput;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Metadata\IriConverterInterface;
use App\Entity\Page\Tab as TabEntity;
use App\ApiResource\Page\Tab as TabResource;
use App\Entity\Page\Permission;
use App\Enum\PermissionEnum;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Bundle\SecurityBundle\Security;

final class TabPostProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $em,
        private IriConverterInterface $iriConverter,
        private ObjectMapperInterface $objectMapper,
    ) {}

    public function process(
        mixed $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = []): TabResource
    {
        \assert($data instanceof TabPostInput);

        $user = $this->security->getUser();
        if(!$user) {
            throw new \InvalidArgumentException('tab.error.user.not_found');
        }

        $parentEntity = null;
        if ($data->parent){
            $parentResource = $this->iriConverter->getResourceFromIri($data->parent);
            $parentEntity = $this->em->getRepository(TabEntity::class)->find($parentResource->id);
            if (!$parentEntity) {
                throw new \InvalidArgumentException('tab.error.parent.not_found');
            }
        }

        $tab = new TabEntity();
        $tab->setName($data->name);
        if($data->nameDefault) $tab->setNameDefault($data->nameDefault);
        $tab->setRoute($data->route);
        if($data->position) $tab->setPosition($data->position);
        $tab->setParent($parentEntity);

        $this->em->persist($tab);
        $this->em->flush();

        $permission = new Permission();
        $permission->setUser($user);
        $permission->setTab($tab);
        $permission->setPermission(PermissionEnum::MANAGE);

        $this->em->persist($permission);
        $this->em->flush();

        $output = $this->objectMapper->map($tab, TabResource::class);
        $output->iri = $this->iriConverter->getIriFromResource($tab->getId());
        $output->permission = PermissionEnum::MANAGE;

        return $output;
    }
}