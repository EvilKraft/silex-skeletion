<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use App\Security\DomainAccessDecisionManagerInterface;

class DomainAccessDecisionManager implements DomainAccessDecisionManagerInterface
{
    private $domains;

    public function __construct(array $domainsAndUsers = array())
    {
        foreach ($domainsAndUsers as $domain => $users) {
            $this->domains[$domain] = (array) $users;
        }
    }

    public function decide(UserInterface $user, $host)
    {
        if (!is_string($host)) {
            throw new \InvalidArgumentException('$host should be a string');
        }

        if (!isset($this->domains[$host])) {
            return false;
        }

        return in_array($user->getUsername(), $this->domains[$host]);
    }
}
