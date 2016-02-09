<?php

namespace App\Repository;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Repository interface.
 *
 * "The Repository pattern just means putting a fa?ade over your persistence
 * system so that you can shield the rest of your application code from having
 * to know how persistence works."
 *
 * A real project would use Doctrine ORM.
 */
interface RepositoryInterface
{
    /**
     * Saves the entity to the database.
     *
     * @param object $entity
     */
    public function save(UserInterface $entity);

    /**
     * Deletes the entity.
     *
     * @param integer $id
     */
    public function delete(UserInterface $entity);

    /**
     * Returns the total number of entities.
     *
     * @return int The total number of entities.
     */
  //  public function getCount();


}