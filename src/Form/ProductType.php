<?php

namespace App\Form;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextType::class, [
                'label'=>'Titre',
                'attr'=>[
                    'placeholder'=> 'Titre'
                ]
            ])
            ->add('description', TextType::class, [
                'label'=>'Description',
                'attr'=>[
                    'placeholder'=> 'Description'
                ]
            ])
            ->add('price', TextType::class, [
                'label'=>'Prix',
                'attr'=>[
                    'placeholder'=> 'Prix'
                ]
            ])
            ->add('stock', TextType::class, [
                'label'=>'Stock',
                'attr'=>[
                    'placeholder'=> 'Stock'
                ]
            ])
            ->add('isActif', ChoiceType::class, [
                'label'=>'Produit actif',
                'choices' => [
                    'Actif'=> true,
                    'Inactif'=> false,
                ]
            ])
            ->add('brand', EntityType::class, [
                'class'=> Brand::class,
                'choice_label'=>'label'
            ])
            ->add('categories', EntityType::class, [
                'class'=> Category::class,
                'choice_label'=>'label',
                // on fait une query pour filtrer uniquement les catégories enfants (pas Chat ni Chien)
                'query_builder' => function (CategoryRepository $cr) {
                    return $cr->createQueryBuilder('c')
                    ->andWhere('c.categoryParent IS NOT NULL');
                },
                // on ajoute ces lignes pour avoir des checkboxs et pour pouvoir sélectionné plusieurs pays 
                'multiple'      => true,
                'expanded'      => true
            ])
            ->add('image', FileType::class, [
                // indication que l'image n'est pas reliée à l'entité du formulaire: on ajoute 'mapped'=>false
                'mapped'=>false
            ])
            ->add('submit', SubmitType::class, [
                'label'=>'Envoyer',
                'attr'=> [
                    'class'=>'btn btn-terracotta'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
