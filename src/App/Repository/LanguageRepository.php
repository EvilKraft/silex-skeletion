<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

use App\Entity\Languages;

/**
 * Language repository
 */
class LanguageRepository extends AbstractRepository
{
    public function findAllActive(){
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('l')
            ->from('App\Entity\Languages', 'l')
            ->where('l.isActive = 1')
            ->orderBy('l.sort', 'DESC');

        return $qb->getQuery()->getResult();
    }
}