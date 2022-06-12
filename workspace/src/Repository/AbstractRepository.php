<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;

/**
 * Class AbstractRepository.
 */
abstract class AbstractRepository extends ServiceEntityRepository
{
    /**
     * Save an object.
     *
     * @param $object
     * @param bool $flush
     *
     * @throws OptimisticLockException
     * @throws \Doctrine\ORM\ORMException
     *
     * @return mixed
     */
    public function save($object, $flush = true)
    {
        $this->_em->persist($object);
        if ($flush) {
            $this->_em->flush($object);
        }

        return $object;
    }

    /**
     * Delete an object.
     *
     * @param mixed $object
     * @param bool  $flush
     *
     * @throws OptimisticLockException
     * @throws \Doctrine\ORM\ORMException
     *
     * @return bool
     */
    public function delete($object, $flush = true)
    {
        $this->_em->remove($object);

        if ($flush) {
            $this->_em->flush($object);
        }

        return true;
    }
}
