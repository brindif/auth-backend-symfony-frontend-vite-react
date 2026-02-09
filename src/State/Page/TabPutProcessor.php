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
use App\Repository\Page\PermissionRepository;
use App\Repository\Auth\UserRepository;
use App\Enum\PermissionEnum;
use App\Entity\Page\Permission;
use App\Entity\Auth\User;

final class TabPutProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $em,
        private IriConverterInterface $iriConverter,
        private ObjectMapperInterface $objectMapper,
        private PermissionRepository $permissionRepository,
        private UserRepository $userRepository,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): TabResource
    {
        \assert($data instanceof TabPutInput);

        $user = $this->security->getUser();
        if(!$user || !$user instanceof User) {
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
        $tab->setParent($parentEntity);
        
        // Manage permissions
        if(is_array($data->permissions)) {
            $oldPermission = $this->permissionRepository->findByTab($tab, $user);
            $old = [];
            foreach($oldPermission as $permission) {
                $old[$permission->getUser()->getId()] = $permission;
            }
            foreach($data->permissions as $permission) {
                $userId = (int) substr(parse_url($permission['user'], PHP_URL_PATH), strrpos($permission['user'], '/') + 1);
                $user = $this->userRepository->find($userId);
                // Create permission
                if($user  && !isset($old[$user->getId()])) {
                    $add = new Permission();
                    $add->setUser($user);
                    $add->setPermission(PermissionEnum::from($permission['permission']));
                    $add->setTab($tab);
                    $this->em->persist($add);
                }
                // Update permission
                elseif($user  && $old[$user->getId()]->getPermission()->value !== $permission['permission']) {
                    $old[$user->getId()]->setPermission(PermissionEnum::from($permission['permission']));
                    $this->em->persist($old[$user->getId()]);
                }
                unset($old[$user->getId()]);
            }
            // Delete permission
            foreach($old as $permission) {
                $this->em->remove($permission);
            }
        }

        $this->em->persist($tab);
        $this->em->flush();

        $output = $this->objectMapper->map($tab, TabResource::class);
        $output->permission = PermissionEnum::MANAGE;

        return $output;
    }
}