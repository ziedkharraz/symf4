<?php

namespace App\Repository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{
    public function dataTable($roles = array(), $data = array(), $page = 0, $max = NULL, $getResult = true, $columnOrder = NULL, $dirOrder = NULL)
    {
        $qb = $this->createQueryBuilder("u");
        $query = isset($data['query']) && $data['query'] ? $data['query'] : null;
        $orX = $qb->expr()->orX();
        foreach ($roles as $role)
            $orX->add($qb->expr()->like("u.roles", "UPPER('%" . $role . "%')"));
        $qb->andWhere($orX);
        if ($query)
            $qb
                ->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->like("UPPER(u.fullName)", "UPPER(:query)"),
                        $qb->expr()->like("UPPER(u.mobile)", "UPPER(:query)"),
                        $qb->expr()->like("UPPER(u.email)", "UPPER(:query)")
                    )
                )
                ->setParameter('query', "%" . $query . "%");
        if ($columnOrder)
            $qb->orderBy("u.$columnOrder", $dirOrder);
        if ($max)
            $preparedQuery = $qb
                ->getQuery()
                ->setMaxResults($max)
                ->setFirstResult($page * $max);
        else
            $preparedQuery = $qb->getQuery();
        return $getResult ? $preparedQuery->getResult() : $preparedQuery;
    }


    public function getFirstByRole($role)
    {
        $qb = $this->createQueryBuilder("u");
        $qb
            ->select("u")
            ->where($qb->expr()->like("u.roles", "UPPER('%$role%')"))
            ->setMaxResults(1);
        return $qb->getQuery()->getOneOrNullResult();
    }
}