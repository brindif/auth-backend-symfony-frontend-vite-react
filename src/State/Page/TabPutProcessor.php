<?php
namespace App\State\Page;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Page\TabPutInput;
use App\Entity\Page\Tab;
use Doctrine\ORM\EntityManagerInterface;

final class TabPutProcessor implements ProcessorInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        \assert($data instanceof TabPutInput);

        $tab = $this->em->find(Tab::class, $uriVariables['id']);
        if (!$tab) {
            throw new \InvalidArgumentException('tab.error.not_found');
        }

        $tab->setName($data->name);
        $tab->setNameDefault($data->nameDefault);
        $tab->setRoute($data->route);
        $tab->setPosition($data->position);
        $tab->setType($data->type);
        $tab->setParent($data->parent);

        $this->em->persist($tab);
        $this->em->flush();

        return $tab;
    }
}