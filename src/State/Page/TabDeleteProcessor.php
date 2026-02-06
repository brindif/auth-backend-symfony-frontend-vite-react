<?php
namespace App\State\Page;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\ApiResource\Page\Tab as TabResource;
use App\Entity\Auth\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Page\Tab as TabEntity;
use Symfony\Component\HttpFoundation\Response;

final class TabDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $em,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        \assert($data instanceof TabResource);

        $user = $this->security->getUser();
        if(!$user || !$user instanceof User) {
            throw new InvalidArgumentException('tab.error.user.not_found');
        }

        $tab = $this->em->find(TabEntity::class, $uriVariables['id']);
        if (!$tab) {
            throw new NotFoundHttpException('tab.error.not_found');
        }
        if(! $this->security->isGranted('manage', $tab)) {
            throw new AccessDeniedException('tab.error.access');
        }

        $tab->setDeletedAt(new \DateTime());
        $this->em->persist($tab);
        $this->em->flush();

        //throw new Response('', Response::HTTP_NO_CONTENT);
    }
}