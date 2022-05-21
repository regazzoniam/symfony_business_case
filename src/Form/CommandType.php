<?php

namespace App\Form;

use App\Entity\Adress;
use App\Entity\Command;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\AdressRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('totalPrice', TextType::class, [
                'label'=>'Prix total',
                'attr'=>[
                    'placeholder'=> 'Prix total'
                ]
            ])
            ->add('user', EntityType::class, [
                'class'=>User::class,
                'choice_label'=>'lastname'
            ])
            // ->add('adress', EntityType::class, [
            //     'class'=>Adress::class,
            //     'choice_label'=>'Adresse',
            //     // on fait une query pour filtrer uniquement les adresses de l'user concerné
            //     'query_builder' => function (AdressRepository $a) {
            //         return $a->createQueryBuilder('a')
            //         ->andWhere('a.users LIKE :adress');
            //         ->setParameter("adress, '%i.");
            //     },
            // ])
            ->add('products', EntityType::class, [
                'class'=>Product::class,
                'choice_label'=>'label',
                // on ajoute ces lignes pour avoir des checkboxs et pour pouvoir sélectionné plusieurs produits 
                'multiple'      => true,
                'expanded'      => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}
