<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Notice;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class NoticeVoter extends Voter
{
    public const CAN_CREATE = 'can_create';
    public const CAN_READ = 'can_read';
    public const CAN_UPDATE = 'can_update';
    public const CAN_DELETE = 'can_delete';

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed  $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject)
    {
        if ( ! ($subject instanceof Notice)) {
            return false;
        }

        return true;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string $attribute
     * @param mixed  $subject
     *
     * @return bool True if we can, false if we can't
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if ( ! ($user instanceof User)) {
            return false;
        }

        if ($user->hasRole(User::ROLE_SUPER_ADMIN)) {
            return true;
        }

        /** @var Notice $notice */
        $notice = $subject;

        if (\in_array($attribute, [self::CAN_CREATE, self::CAN_UPDATE, self::CAN_DELETE], true)) {
            if ($notice->getContributor()->hasImpersonator($user)) {
                return true;
            }
        } elseif (self::CAN_READ == $attribute) {
            // add visibility shenanigans here as-needed?
            return true;
        }

        return false;
    }
}
