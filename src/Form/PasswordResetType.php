<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordResetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', TextType::class, [
                'label'=>'Entrer votre nouveau mot de passe',
                'attr'=>[
                    'placeholder'=> 'Password'
                ]
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
            // Configure your form options here
        ]);
    }
}
