<?php

namespace App\Service;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 */
class AbstractService
{

    /**
     * @param EntityManagerInterface $em
     * @param $entityName
     */
    protected function __construct(EntityManagerInterface $em, $entityName)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($entityName);
    }

    protected function getEntityManager()
    {
        return $this->em;
    }
}