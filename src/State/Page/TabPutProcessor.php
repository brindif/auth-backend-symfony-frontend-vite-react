<?php
namespace App\State\Page;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Page\TabPutInput;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Metadata\IriConverterInterface;
use App\Entity\Page\Tab as TabEntity;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use App\ApiResource\Page\Tab as TabResource;
use App\Enum\PermissionEnum;

final class TabPutProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $em,
        private IriConverterInterface $iriConverter,
        private ObjectMapperInterface $objectMapper,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): TabResource
    {
        \assert($data instanceof TabPutInput);

        $user = $this->security->getUser();
        if(!$user) {
            throw new \InvalidArgumentException('tab.error.user.not_found');
        }

        $tab = $this->em->find(TabEntity::class, $uriVariables['id']);
        if (!$tab) {
            throw new \InvalidArgumentException('tab.error.not_found');
        }

        $parentEntity = null;
        if ($data->parent){
            $parentResource = $this->iriConverter->getResourceFromIri($data->parent);
            $parentEntity = $this->em->getRepository(TabEntity::class)->find($parentResource->id);
            if (!$parentEntity) {
                throw new \InvalidArgumentException('tab.error.parent.not_found');
            }
        }

        $tab->setName($data->name);
        if($data->nameDefault) $tab->setNameDefault($data->nameDefault);
        $tab->setRoute($data->route);
        if($data->position) $tab->setPosition($data->position);
        if($data->type) $tab->setType($data->type);
        $tab->setParent($parentEntity);

        $this->em->persist($tab);
        $this->em->flush();

        $output = $this->objectMapper->map($tab, TabResource::class);
        $output->permission = PermissionEnum::MANAGE;

        return $output;
    }
}