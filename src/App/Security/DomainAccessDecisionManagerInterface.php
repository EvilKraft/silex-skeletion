<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

interface DomainAccessDecisionManagerInterface
{
    public function decide(UserInterface $user, $host);
}
