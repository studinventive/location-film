<?php

namespace App\Serializer;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

/**
 * This custom denormalizer automatically hash created User's password.
 */
class CurrentUserDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private const ALREADY_CALLED_DENORMALIZER = 'CurrentUserDenormalizerCalled'; // Avoid denormalizer infinite loop 

    public function __construct(
        private Security $security,
        private UserPasswordHasherInterface $hasher)
    {
        
    }

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED_DENORMALIZER] = true;

        /** @var User $user */
        $user = $this->denormalizer->denormalize($data, $type, $format, $context);

        $rawPassword = $user->getPassword();
        $user->setPassword($this->hasher->hashPassword($user, $rawPassword));

        return $user;
    }

    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = []): bool
    {
        $alreadyCalled = $context[self::ALREADY_CALLED_DENORMALIZER] ?? false;
        return (User::class === $type && false === $alreadyCalled);
    }
}
