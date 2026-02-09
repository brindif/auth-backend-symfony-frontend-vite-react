<?php
namespace App\State\Note;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Note\NotePutInput;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Metadata\IriConverterInterface;
use App\Entity\Page\Tab as TabEntity;
use App\Entity\Page\Note as NoteEntity;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use App\ApiResource\Page\Note as NoteResource;
use App\Repository\Page\PermissionRepository;
use App\Repository\Auth\UserRepository;
use App\Entity\Auth\User;

final class NotePutProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $em,
        private IriConverterInterface $iriConverter,
        private ObjectMapperInterface $objectMapper,
        private PermissionRepository $permissionRepository,
        private UserRepository $userRepository,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): NoteResource
    {
        \assert($data instanceof NotePutInput);

        $user = $this->security->getUser();
        if(!$user || !$user instanceof User) {
            throw new \InvalidArgumentException('note.error.user.not_found');
        }

        $note = $this->em->find(NoteEntity::class, $uriVariables['id']);
        if (!$note) {
            throw new \InvalidArgumentException('note.error.not_found');
        }

        $tabEntity = null;
        if ($data->tab){
            $tabResource = $this->iriConverter->getResourceFromIri($data->tab);
            $tabEntity = $this->em->getRepository(TabEntity::class)->find($tabResource->id);
            if (!$tabEntity) {
                throw new \InvalidArgumentException('note.error.tab.not_found');
            }
        }

        $note->setName($data->name);
        if($data->nameDefault) $note->setNameDefault($data->nameDefault);
        if($data->position) $note->setPosition($data->position);
        $note->setTab($tabEntity);

        $this->em->persist($note);
        $this->em->flush();

        $output = $this->objectMapper->map($note, NoteResource::class);

        return $output;
    }
}