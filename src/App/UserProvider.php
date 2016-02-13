<?php

namespace App;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserProvider implements UserProviderInterface
{
    public function __construct($app)
    {
        $this->app = $app;
    }
    public function loadUserByUsername($username)
    {
        $stmt = $this->app['db']->executeQuery("SELECT * FROM user WHERE username = ? OR email = ?", array($username, $username));
        if (!$user = $stmt->fetch()) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }
        return new User($user['username'], $user['pwd_hash'], explode(',', $user['roles']), true, true, true, true);
    }
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }
        return $this->loadUserByUsername($user->getUsername());
    }
    public function supportsClass($class)
    {
        return $class === 'Symfony\Component\Security\Core\User\User';
    }
}