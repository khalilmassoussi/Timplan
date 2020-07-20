<?php

namespace TimSoft\FeuilleRapportInterventionBundle\Security;

use TimSoft\GeneralBundle\Entity\FeuilleDePresence;
use TimSoft\GeneralBundle\Entity\Utilisateur;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class FeuilleVoter extends Voter
{
    // these strings are just invented: you can use anything
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';
    const DOWNLOAD = 'download';

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::VIEW, self::EDIT, self::DELETE, self::DOWNLOAD))) {
            return false;
        }

        // only vote on Users objects inside this voter
        if (!$subject instanceof FeuilleDePresence) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        // verifier si l'utilisateur est connecté
        $user = $token->getUser();

        if (!$user instanceof Utilisateur) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Utilisateur object, thanks to supports
        /** @var Utilisateur $Utilisateur */
        //$Utilisateur = $subject;
        if ( $user->getRoleUtilisateur() === "ROLE_ADMIN")
        {
            return true;
        }
        
        // verifier si l'utilisateur est l'utilisateur connecté
        if ( $user === $subject->getIntervenant())
        {
            return true;
        }
        
        // verifier si l'utilisateur est l'utilisateur connecté
        if ( $user->getClient() !== $subject->getClient())
        {
            return false;
        }
        return true;
    }
}

