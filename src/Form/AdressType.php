<?php

namespace App\Form;

use App\Entity\Adress;
use App\Entity\City;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('streetNumber', TextType::class, [
                'label'=>'Numéro de rue',
                'attr'=>[
                    'placeholder'=> 'Numéro de rue'
                ]
            ])
            ->add('streetName', TextType::class, [
                'label'=>'Nom de rue',
                'attr'=>[
                    'placeholder'=> 'Nom de rue'
                ]
            ])
            ->add('city', EntityType::class, [
                'label'=>'Ville',
                'class'=> City::class,
                'choice_label'=>'name'
            ])
            ->add('submit', SubmitType::class,[
                'label'=>'Envoyer',
                'attr' => [
                    'class' => 'btn btn-terracotta'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Adress::class,
        ]);
    }
}
