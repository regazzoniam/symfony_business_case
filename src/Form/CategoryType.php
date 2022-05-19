<?php

namespace App\Form;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
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
            ->add('categoryParent', EntityType::class, [
                'class'=> Category::class,
                'choice_label'=>'label',
                // on fait une query pour filtrer uniquement les catégories enfants (pas Chat ni Chien)
                'query_builder' => function (CategoryRepository $cr) {
                    return $cr->createQueryBuilder('c')
                    ->andWhere('c.categoryParent IS NULL');
                },
                // on ajoute ces lignes pour avoir des checkboxs et pour pouvoir sélectionné plusieurs pays 
                'multiple'      => false,
                'expanded'      => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
