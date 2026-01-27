<?php
namespace App\State\Page;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Page\TabPostInput;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Metadata\IriConverterInterface;
use App\Entity\Page\Tab as TabEntity;
use App\ApiResource\Page\Tab as TabResource;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

final class TabPostProcessor implements ProcessorInterface
{
    public function __construct(
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
        if($data->type) $tab->setType($data->type);
        $tab->setParent($parentEntity);

        $this->em->persist($tab);
        $this->em->flush();

        $output = $this->objectMapper->map($tab, TabResource::class);
        $output->iri = $this->iriConverter->getIriFromResource($tab->getId());

        return $output;
    }
}