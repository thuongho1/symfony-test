<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 */
class CategoryService extends AbstractService
{
    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $this->validator = $validator;
        parent::__construct($em, Category::class);
    }

    public function create($data): bool
    {
        $category = new Category();
        $category->setTitle($data['title']);
        $category->setEId($data['eId'] ?? NULL);


        $errors = $this->validator->validate($category);
        if (count($errors) == 0) {
            $this->repository->add($category, true);
            return TRUE;
        }
        return FALSE;
    }
//    protected function getCategoryByEId($categoriesEId)
//    {
//        $categories = [];
//        foreach ($categoriesEId as $category_eId) {
//            $category = $this->repository->find($category_eId);
//            if (empty($category)) {
//                $category = $this->repository->findByEId($category_eId);
//            }
//            if (empty($category)) {
//                continue;
//            }
//            $categories[] = $category;
//        }
//        return $categories;
//
//    }
}