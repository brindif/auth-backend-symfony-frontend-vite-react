<?php

namespace App\Doctrine\Page;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Page\Permission;
use App\Entity\Page\Tab;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class TabPermissionExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(private Security $security)
    {
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        $this->addReadConstraint($queryBuilder, $queryNameGenerator, $resourceClass);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        $this->addReadConstraint($queryBuilder, $queryNameGenerator, $resourceClass);
    }

    private function addReadConstraint(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
    ): void {
        if (Tab::class !== $resourceClass) {
            return;
        }

        $user = $this->security->getUser();
        if (null === $user) {
            $queryBuilder->andWhere('1 = 0');

            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $permAlias = $queryNameGenerator->generateJoinAlias('permission');
        $queryBuilder
          ->innerJoin(
              Permission::class,
              $permAlias,
              'WITH',
              sprintf('%s.tab = %s AND %s.user = :current_user', $permAlias, $rootAlias, $permAlias)
          )
          ->setParameter('current_user', $user);
    }
}
