<?php

namespace AppBundle\Security\SecurityVoters;

use AppBundle\Entity\LDAP\PbnlMailAlias;
use AppBundle\Model\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MailAliasVoter extends Voter
{
    // these strings are just invented: you can use anything
    const EDIT = 'edit';
    const REMOVE = 'remove';
    const VIEW = 'view';
    const ADD = 'add';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::EDIT, self::ADD, self::REMOVE, self::VIEW))) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof PbnlMailAlias) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $mailAlias, TokenInterface $token)
    {
        $loggedInUser = $token->getUser();

        if (!$loggedInUser instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }


        if (in_array($loggedInUser->getMail(), $mailAlias->getForward())) {
            return true;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($mailAlias, $token);
            case self::REMOVE:
                return $this->canRemove($mailAlias, $token);
            case self::VIEW:
                return $this->canView($mailAlias, $token);
            case self::ADD:
                return $this->canAdd($mailAlias, $token);
        }

        throw new \LogicException('This code should not be reached!');
    }


    private function canEdit(PbnlMailAlias $mailAlias, TokenInterface $token)
    {
        if ($this->decisionManager->decide($token, array("ROLE_EDIT_ALL_MAILALIAS"))) {
            return true;
        }
        return false;
    }

    private function canRemove(PbnlMailAlias $mailAlias, TokenInterface $token)
    {
        if ($this->decisionManager->decide($token, array("ROLE_REMOVE_ALL_MAILALIAS"))) {
            return true;
        }
        return false;
    }

    private function canView(PbnlMailAlias $mailAlias, TokenInterface $token)
    {
        if ($this->decisionManager->decide($token, array("ROLE_VIEW_ALL_MAILALIAS"))) {
            return true;
        }
        return false;
    }

    private function canAdd($mailAlias, $token)
    {
        return true;
    }
}
