<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, ValidatorInterface $validator)
    {
        parent::__construct($registry, Product::class);
        $this->validator = $validator;
        $this->categoryRepo = $this->getEntityManager()->getRepository(Category::class);
    }

    public function add(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByEId($eId)
    {
        return $this->findOneBy(['eId' => $eId]);
    }

    public function createOrUpdateFromArray($data)
    {
        if (!empty($data['eId'])) {
            $product = $this->findByEId($data['eId']);
        }
        if (empty($product)) {
            $product = new Product();
            $product->setEId($data['eId'] ?? NULL);

        }

        $product->setTitle($data['title']);
        $product->setPrice(floatval($data['price']));

        $categories = $data['categoriesId'] ?? $data['categoriesEId'] ?? [];
        if (!empty($data['categoriesEId'])) {
            $categories = $this->getCategoryByEId($data['categoriesEId']);
        }
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $product->addCategory($category);
            }
        }

        $errors = $this->validator->validate($product);
        if (count($errors) == 0) {
            $this->add($product, true);
            return TRUE;
        }
        return FALSE;
    }

    protected function getCategoryByEId($categoriesEId)
    {
        $categories = [];
        foreach ($categoriesEId as $category_eId) {
            $category = $this->categoryRepo->find($category_eId);
            if (empty($category)) {
                $category = $this->categoryRepo->findByEId($category_eId);
            }
            if (empty($category)) {
                continue;
            }
            $categories[] = $category;
        }
        return $categories;

    }
//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
