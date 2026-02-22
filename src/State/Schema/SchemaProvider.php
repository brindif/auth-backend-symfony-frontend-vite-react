<?php
namespace App\State\Schema;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Orm\State\ItemProvider as DoctrineItemProvider;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\IriConverterInterface;
use App\ApiResource\Content\Schema;
use App\Entity\Content\Schema as SchemaEntity;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Doctrine\Orm\Paginator;

use ApiPlatform\State\Pagination\TraversablePaginator;
use ArrayIterator;

final class SchemaProvider implements ProviderInterface
{
  public function __construct(
    private ObjectMapperInterface $mapper,
    private IriConverterInterface $iriConverter,
    #[Autowire(service: DoctrineItemProvider::class)]
    private ProviderInterface $doctrineItemProvider,
    #[Autowire(service: CollectionProvider::class)]
    private ProviderInterface $doctrineCollectionProvider,
  ) {}


  public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
  {
    if ($operation instanceof CollectionOperationInterface) {
      $paginator = $this->doctrineCollectionProvider->provide($operation, $uriVariables, $context);
      assert($paginator instanceof Paginator);

      $schemas = [];
      foreach ($paginator as $entity) {
        $schemas[] = $this->mapWithIri($entity);
      }
      
      return new TraversablePaginator(
        new ArrayIterator($schemas),
        $paginator->getTotalItems(),
        $paginator->getCurrentPage(),
        $paginator->getItemsPerPage()
      );
    }

    $entity = $this->doctrineItemProvider->provide($operation, $uriVariables, $context);
    return $entity ? $this->mapWithIri($entity) : null;
  }

  private function mapWithIri(SchemaEntity $entity): Schema
  {
    $schema = $this->mapper->map($entity, Schema::class);
    $tabs = [];
    foreach ($schema->tabs as $tab) {
      $tabs[] =  $this->iriConverter->getIriFromResource($tab);
    }
    $schema->tabs = $tabs;
    
    return $schema;
  }
}
