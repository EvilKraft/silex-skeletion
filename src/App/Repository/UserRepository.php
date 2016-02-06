<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Doctrine\ORM\NoResultException;

/**
 * User repository
 */
class UserRepository extends EntityRepository implements RepositoryInterface, UserProviderInterface
{

    public function save($item)
    {
        // TODO
        // if($item instanceof $this->getEntityName())

        $this->_em->persist($item);
        $this->_em->flush();
    }



    /**
     * {@inheritDoc}
     */
    public function loadUserByUsername($username)
    {
        $q = $this->createQueryBuilder('u')
            ->where('u.username = :username OR u.mail = :mail')
            ->setParameter('username', $username)
            ->setParameter('mail', $username)
            ->getQuery();

        try {
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            throw new UsernameNotFoundException(sprintf('User "%s" not found.', $username));
        }
        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        //return 'App\Entity\Users' === $class;

        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }
}