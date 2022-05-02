<?php

namespace App\Security\Voter;

use App\Entity\Mention;
use App\Entity\User;
use App\Repository\MentionRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MentionVoter extends Voter
{
    public const NEW = 'NEW';
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';

    public function __construct(private MentionRepository $mentionRepository)
    {
        
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::NEW, self::EDIT, self::DELETE])
            && $subject instanceof Mention;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Mention $mention */
        $mention = $subject;

        switch ($attribute) {
            case self::NEW:
                return $this->canAddNew($mention, $user);
                break;
            case self::EDIT:
                return $this->canEdit($mention, $user);
                break;
            case self::DELETE:
                return $this->canDelete($mention, $user);
                break;    
        }

        return false;
    }

    private function canAddNew(Mention $mention, User $user)
    {
        return (bool) !$this->mentionRepository->UserHasMentionOnMovie($mention->getMovie(), $user);
    }

    private function canEdit(Mention $mention, User $user)
    {
        return $this->canDelete($mention, $user);
    }

    private function canDelete(Mention $mention, User $user)
    {
        return $mention->getUser()->getId() === $user->getId();
    }
}
