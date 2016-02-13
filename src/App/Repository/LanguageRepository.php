<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

use App\Entity\Languages;

/**
 * Language repository
 */
class LanguageRepository extends EntityRepository
{

    public function save(\App\Entity\Languages $item)
    {
        $this->_em->persist($item);
        $this->_em->flush();
    }

    public function delete(\App\Entity\Languages $item)
    {
        $this->_em->remove($item);
        $this->_em->flush();
    }

    public function findAllActive(){
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('l')
            ->from('App\Entity\Languages', 'l')
            ->where('l.isActive = 1')
            ->orderBy('l.sort', 'DESC');

        return $qb->getQuery()->getResult();
    }

}