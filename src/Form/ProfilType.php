<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'attr'=>[
                    'placeholder'=> 'Email'
                ]
            ])
            ->add('firstName', TextType::class, [
                'attr'=>[
                    'placeholder'=> 'firstName'
                ]
            ])
            ->add('lastName', TextType::class, [
                'attr'=>[
                    'placeholder'=> 'lastName'
                ]
            ])
            // ->add('adresses')
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
            'data_class' => User::class,
        ]);
    }
}
