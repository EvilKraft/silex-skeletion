<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity;

class OrderRepository extends EntityRepository
{
    public function findNotFinished(){

/*
        // First get the EM handle
        // and call the query builder on it
        $qb = $this->_em->createQueryBuilder();
        $qb->select('o')
            ->from('App\Entity\Orders', 'o')
            ->where('o.finishedAt IS NULL')
            ->orderBy('o.createdAt');

        return $qb->getQuery()->getResult();
*/
/*
        $qb = $this->_em->createQueryBuilder();
        $qb
            ->select('o', 'c')
            ->from('App\Entity\Orders', 'o')
            ->leftJoin(
                'App\Entity\Clients',
                'c',
            //    \Doctrine\ORM\Query\Expr\Join::WITH,
                'WITH',
                'o.clientId = c.id'
            )
            ->where('o.finishedAt IS NULL')
            ->orderBy('o.createdAt', 'DESC');

        return $qb->getQuery()->getResult();

*/
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('o', 'c', 'd', 'm')
            ->from('App\Entity\Orders', 'o')
            ->leftJoin('o.client', 'c', 'ON')
            ->leftJoin('o.driver', 'd', 'ON')
            ->join('d.mobile', 'm')
            ->where('o.finishedAt IS NULL')
            ->orderBy('o.createdAt', 'DESC');
        return $qb->getQuery()->getResult();
        //return $qb->getQuery()->getArrayResult();


   //     $query = $this->_em->createQuery('SELECT o, c, d FROM App\Entity\Orders o JOIN o.client c JOIN o.driver d WHERE o.finishedAt IS NULL ORDER BY o.createdAt DESC');
    //    return $query->getResult();


//        $query = $this->_em->createQuery("SELECT o, c FROM App\Entity\Orders o JOIN App\Entity\Clients ñ WITH o.clientId = c.id WHERE o.finishedAt IS NULL");
//        return $query->getResult();



    }


    public function findFinishedByDate($start = null, $end = null){

        if(is_null($start)){
            $start = new \DateTime(date('Y-m-d')." 00:00:00");
            $start->modify('-3 day');
        }else{
            $start = new \DateTime($start." 00:00:00");
        }

        if(is_null($end)){
            $end = new \DateTime(date('Y-m-d')." 23:59:59");
        }else{
            $end = new \DateTime($end." 23:59:59");
        }



        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('o', 'c', 'd', 'm')
            ->from('App\Entity\Orders', 'o')
            ->leftJoin('o.client', 'c', 'ON')
            ->leftJoin('o.driver', 'd', 'ON')
            ->join('d.mobile', 'm')
            ->where('o.finishedAt IS NOT NULL')
            ->andWhere('o.createdAt >= :param1')
            ->andWhere('o.createdAt <= :param2')
            ->orderBy('o.createdAt', 'DESC');

        $qb->setParameters(array(
            'param1' => $start->format('U'),
            'param2' => $end->format('U'),
        ));

        return $qb->getQuery()->getResult();
    }
}