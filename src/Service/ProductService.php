<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 *
 * @method ProductRepository|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductRepository|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductRepository[]    findAll()
 * @method ProductRepository[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductService extends AbstractService
{
    protected $categoryRepo;

    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator)
    {
        parent::__construct($em, Product::class);
        $this->validator = $validator;
        $this->categoryRepo = $em->getRepository(Category::class);
        dd($this->validator);
    }

    public function create($data): bool
    {
        $product = new Product();
        $product->setTitle($data['title']);
        $product->setPrice(floatval($data['price']));
        $product->setEId($data['eId'] ?? NULL);

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
            $this->repository->add($product, true);
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
}