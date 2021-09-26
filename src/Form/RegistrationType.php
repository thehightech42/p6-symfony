<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('username')
            ->add('password', RepeatedType::class, [
                'type'=>PasswordType::class,
                'invalid_message'=> "Les mots de saisis ne sont pas les mêmes", 
                'options'=>['attr' => ['class'=>'password-field']],
                'required'=>true,
                'first_options'=>['label'=>'Mot de passe', 'attr' => ['placeholder'=>'Votre mot de passe']],
                'second_options'=>['label'=>'Confirmation Mot de passe', 'attr' => ['placeholder'=>'Confirmer votre mot de passe']]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
