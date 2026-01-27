<?php
namespace App\State\Page;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Page\TabPostInput;
use App\Entity\Page\Tab;
use Doctrine\ORM\EntityManagerInterface;

final class TabPostProcessor implements ProcessorInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        \assert($data instanceof TabPostInput);

        $tab = new Tab();
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