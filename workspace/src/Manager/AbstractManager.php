<?php

namespace App\Manager;

use App\Entity\WebsitePhotoCrawler;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Class AbstractManager.
 */
abstract class AbstractManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var EntityRepository
     */
    protected $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(static::getEntityClassName());
    }

    public function save($object): void
    {
        $this->repository->save($object);
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function findById(int $id): ?object
    {
        return $this->repository->find($id);
    }

    public function findOneByCriterias(array $criterias): ?object
    {
        return $this->repository->findOneBy($criterias);
    }

    public function findByCriterias(array $criterias, ?array $orderBy = null, $limit = null, $offset = null): ?array
    {
        return $this->repository->findBy($criterias, $orderBy, $limit, $offset);
    }

    abstract protected function getEntityClassName();
}
