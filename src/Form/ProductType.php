<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Unique;

class ProductType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', NULL, ['constraints' => [new Length(['min' => 3])], 'attr' => ['minlength' => 3, 'maxlength' => 12]])
            ->add('price', NumberType::class, ['scale' => 2, 'html5' => TRUE, 'attr' => ['min' => 0, 'max' => 200, 'step' => 0.01]])
            ->add('eId');

        $builder->add('categories', EntityType::class, array(
            'class' => Category::class,
            'multiple' => true,
            'expanded' => true,
            'choice_label' => 'title',
            'placeholder' => 'Select a value',
            'empty_data' => null,
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
