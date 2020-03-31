<?php

namespace App\Security\SecurityVoters;

use App\Model\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    // these strings are just invented: you can use anything
    const EDIT = 'edit';
    const REMOVE = 'remove';
    const VIEW = 'view';
    const CHANGEPASSWORD = 'changePassword';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::EDIT, self::REMOVE, self::VIEW, self::CHANGEPASSWORD))) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $user, TokenInterface $token)
    {
        $loggedInUser = $token->getUser();

        if (!$loggedInUser instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }


        if ($user->getUid() == $token->getUser()->getUid()) {
            return true;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($user, $token);
            case self::REMOVE:
                return $this->canRemove($user, $token);
            case self::VIEW:
                return $this->canView($user, $token);
            case self::CHANGEPASSWORD:
                return $this->canChangePassword($user, $token);
        }

        throw new \LogicException('This code should not be reached!');
    }


    private function canEdit(User $user, TokenInterface $token)
    {
        if ($this->decisionManager->decide($token, array("ROLE_EDIT_ALL_USERS"))) {
            return true;
        } elseif ($this->decisionManager->decide($token, array("ROLE_stavo"))
            && $user->getStamm() == $token->getUser()->getStamm()) {
            return true;
        }

        return false;
    }

    private function canRemove(User $user, TokenInterface $token)
    {
        if ($this->decisionManager->decide($token, array("ROLE_REMOVE_ALL_USERS"))) {
            return true;
        }
        return false;
    }

    private function canView(User $user, TokenInterface $token)
    {
        if ($this->decisionManager->decide($token, array("ROLE_elder"))) {
            return true;
        }

        return false;
    }

    private function canChangePassword(User $user, TokenInterface $token)
    {
        if ($this->decisionManager->decide($token, array("ROLE_CHANGEPASSWORD_ALL_USERS"))) {
            return true;
        } elseif ($this->decisionManager->decide($token, array("ROLE_stavo"))
            && $user->getStamm() == $token->getUser()->getStamm()) {
            return true;
        }

        return false;
    }
}
