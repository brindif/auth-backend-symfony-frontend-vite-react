<?php
namespace App\State\Page;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Page\TabPutInput;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Metadata\IriConverterInterface;
use App\Entity\Page\Tab as TabEntity;

final class TabPutProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private IriConverterInterface $iriConverter
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        \assert($data instanceof TabPutInput);

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

        return $tab;
    }
}