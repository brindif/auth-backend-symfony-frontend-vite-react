<?php
namespace App\State\Auth;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Auth\UserPutInput;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Auth\User as UserEntity;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use App\ApiResource\Auth\User as UserResource;
use App\Entity\Auth\User;

final class UserPutProcessor implements ProcessorInterface
{
  public function __construct(
    private Security $security,
    private EntityManagerInterface $em,
    private ObjectMapperInterface $objectMapper,
  ) {}

  public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): UserResource
  {
    \assert($data instanceof UserPutInput);

    $user = $this->security->getUser();
    if(!$user || !$user instanceof User) {
      throw new \InvalidArgumentException('user.error.user.not_found');
    }

    $user = $this->em->find(UserEntity::class, $uriVariables['id']);
    if (!$user) {
      throw new \InvalidArgumentException('user.error.not_found');
    }

    if(is_array($data->roles)){
      $data->roles = array_values(array_filter(array_unique($data->roles)));
    }

    $user->setName($data->name);
    $user->setEmail($data->email);
    $user->setRoles($data->roles);
    $user->setIsVerified($data->isVerified);

    $this->em->persist($user);
    $this->em->flush();

    $output = $this->objectMapper->map($user, UserResource::class);

    return $output;
  }
}