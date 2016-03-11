<?php


namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Gedmo\Tree\Traits\Repository\ORM\NestedTreeRepositoryTrait;

class ContentRepository extends EntityRepository
{
    use NestedTreeRepositoryTrait; // or MaterializedPathRepositoryTrait or ClosureTreeRepositoryTrait.

    public function __construct(\Doctrine\ORM\EntityManager $em, \Doctrine\ORM\Mapping\ClassMetadata $class)
    {
        parent::__construct($em, $class);

        $this->initializeTreeRepository($em, $class);
    }

    public function save($item)
    {
        $this->_em->persist($item);
        $this->_em->flush();
    }

    public function delete($item){
        $this->_em->remove($item);
        $this->_em->flush();
    }
}