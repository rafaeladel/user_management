<?php

namespace Tsk\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class RoleRepository extends EntityRepository
{
    public function getRoleIfExists($rolename)
    {
        $q = $this->createQueryBuilder('r')
                    ->select('r')
                    ->where('r.role = :role')
                    ->setParameter('role', $rolename)
                    ->getQuery();

        try
        {
            $role = $q->getSingleResult();
        }
        catch (NoResultException $e)
        {
            $role = null;
        }

        return $role;
    }
}
