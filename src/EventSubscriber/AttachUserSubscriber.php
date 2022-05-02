<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Comment;
use App\Entity\Mention;
use App\Entity\Rent;
use App\Entity\User;
use App\Exception\UserInPayloadNotFoundException;
use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AttachUserSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private UserRepository $userRepository,
    )
    {

    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['attachUser', EventPriorities::PRE_WRITE],
        ];
    }

    public function attachUser(ViewEvent $event)
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        $token = $this->tokenStorage->getToken();

        if (!$token || !in_array($method, [Request::METHOD_POST, Request::METHOD_PUT])) {
            return;
        }

        $payloadUser = $token->getUser();

        $user = $this->userRepository->findOneBy(['id' => $payloadUser?->getId()]);

        if (null === $user) {
            throw new UserInPayloadNotFoundException('Incorrect User or not found User in JSON WEB Token Payload');
        }
        

        if ($entity instanceof Mention && Request::METHOD_POST === $method) {
            $this->attachToMention($entity, $user);
        }

        if ($entity instanceof Comment) {
            $this->attachToComment($entity, $user, $method);
        }

        if ($entity instanceof Rent && Request::METHOD_POST === $method) {
            $this->attachToRent($entity, $user);
        }
    }

    private function attachToMention(Mention $mention, User $user)
    {
        $mention->setUser($user);
    }

    private function attachToComment(Comment $comment, User $user, string $method)
    {
        if (Request::METHOD_POST === $method) {
            $comment->setUser($user);
        } elseif (Request::METHOD_PUT === $method) {
            $comment->setUpdatedAt(new \DateTimeImmutable());
        }
    }

    private function attachToRent(Rent $rent, User $user)
    {
        $rent->setUser($user);
    }
}
