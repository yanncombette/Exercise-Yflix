<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ReviewAddVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        if ($attribute === 'MOVIE_REVIEW_ADD' && $subject instanceof \App\Entity\Movie)
        {
            return true;
        } 
        else 
        {
            return false;
        }
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        // on a également accès à l'utilisateur connecté actuellement grace à l'objet $token ( $token->getUser() )
        return true;
    }
}
