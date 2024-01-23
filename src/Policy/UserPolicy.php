<?php
namespace App\Policy;

use App\Model\Entity\User;
use Authorization\IdentityInterface;

class UserPolicy
{
    public function canAdd(IdentityInterface $user, User $user)
    {
        //Tous les utilisateurs authentifiés peuvent créer des articles.
        return true;
    }
}
?>