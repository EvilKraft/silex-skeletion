<?php
/**
 * Created by PhpStorm.
 * User: Kraft
 * Date: 31.03.2016
 * Time: 12:12
 */

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class AbstractRepository extends EntityRepository implements RepositoryInterface
{
    public function save($item)
    {
        $this->checkInstance($item);

        $this->_em->persist($item);
        $this->_em->flush();
    }

    public function delete($item)
    {
        $this->checkInstance($item);

        $this->_em->remove($item);
        $this->_em->flush();
    }

    public function getCount()
    {
        $query = $this->_em->createQuery('SELECT COUNT(i.id) FROM '.$this->getEntityName().' i');
        return $query->getSingleScalarResult();
    }

    /**
     * Whether this provider supports the given user class
     *
     * @param string $class
     * @return Boolean
     */
    public function supportsClass($class)
    {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }

    protected function checkInstance($item){
        $class = get_class($item);

        if (!$this->supportsClass($class)) {
            throw new InvalidArgumentException(sprintf('Instances of "%s" are not supported.', $class));
        }
    }
}