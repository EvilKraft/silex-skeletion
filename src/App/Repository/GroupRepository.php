<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Group repository
 */
class GroupRepository extends AbstractRepository
{

    public function getRoleHierarchy(){

        $hierarchy = array();

        foreach($this->findAll() as $group){
            $hierarchy[$group->getName()] = $group->getRoles();
        }

        return $hierarchy;
    }
}