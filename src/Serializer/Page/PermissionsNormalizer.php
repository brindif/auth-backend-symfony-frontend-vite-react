<?php

namespace App\Serializer\Page;

use App\ApiResource\Page\Tab as TabResource;
use App\Entity\Auth\User;
use App\Enum\PermissionEnum;
use App\Repository\Page\PermissionRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use ApiPlatform\Metadata\IriConverterInterface;

final class PermissionsNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
  use NormalizerAwareTrait;

  private const ALREADY_CALLED = 'tab_permissions_normalizer_already_called';

  public function __construct(
    private readonly Security $security,
    private readonly PermissionRepository $permissionRepository,
    private IriConverterInterface $iriConverter,
  ) {}

  public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
  {
    if (isset($context[self::ALREADY_CALLED])) {
      return false;
    }

    return $data instanceof TabResource;
  }

  public function normalize(mixed $data, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
  {
    $context[self::ALREADY_CALLED] = true;

    /** @var TabResource $data */
    $user = $this->security->getUser();
    $currentUser = $user instanceof User ? $user : null;

    // Search permission for current user
    if (null !== $currentUser && null !== $data->id) {
      $permission = $this->permissionRepository->findOneForUserAndTabId($data->id, $currentUser);
      $data->permission = $permission?->getPermission();
    }

    // Filter users permissions when current user can manage and without current user permission
    if (null !== $currentUser && is_array($data->permissions) && $data->permission === PermissionEnum::MANAGE) {
      $permissions = [];
      foreach($data->permissions as $permission) {
        if ($permission->getUser() && $permission->getUser()->getId() === $currentUser->getId()) {
          $permissions[] = [
            'user' => $this->iriConverter->getIriFromResource($permission->getUser()),
            'permission' => $permission->getPermission()?->value,
          ];
        }
      }
      $data->permissions = $permissions;
    } else {
      $data->permissions = [];
    }

    return $this->normalizer->normalize($data, $format, $context);
  }

  public function getSupportedTypes(?string $format): array
  {
    return [TabResource::class => false];
  }
}
