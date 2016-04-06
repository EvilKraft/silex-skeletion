<?php


namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Gedmo\Tree\Traits\Repository\ORM\NestedTreeRepositoryTrait;

class ContentRepository extends AbstractRepository
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


    public function buildTreeArrayOfObjects(array $nodes)
    {
        $meta = $this->getClassMetadata();
        $config = $this->listener->getConfiguration($this->_em, $meta->name);
        $nestedTree = array();
        $l = 0;

        if (count($nodes) > 0) {
            // Node Stack. Used to help building the hierarchy
            $stack = array();
            foreach ($nodes as $child) {
                $item = $child;
                $item->{$this->getChildrenIndex()} = array();
                // Number of stack items
                $l = count($stack);

                // Check if we're dealing with different levels
                while ($l > 0 && $stack[$l - 1]->getLvl() >= $item->getLvl()) {
                    array_pop($stack);
                    $l--;
                }
                // Stack is empty (we are inspecting the root)
                if ($l == 0) {
                    // Assigning the root child
                    $i = count($nestedTree);
                    $nestedTree[$i] = $item;
                    $stack[] = &$nestedTree[$i];
                } else {
                    // Add child to parent
                    $i = count($stack[$l - 1]->{$this->getChildrenIndex()});
                    $stack[$l - 1]->{$this->getChildrenIndex()}[$i] = $item;
                    $stack[] = &$stack[$l - 1]->{$this->getChildrenIndex()}[$i];
                }
            }
        }

        return $nestedTree;
    }
}