<?php

namespace App\Form;

use App\Entity\Figure;
use App\Entity\GroupeFigure;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ControlFigureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('groupe', EntityType::class, array(
                'class'=> GroupeFigure::class,
                'choice_label'=> 'title')
                )
            ->add('title',TextType::class, [
                'label'=>'Titre', 
                'attr'=>[
                    'placeholder'=>'Ecrit ici son petit nom ;)'
                ]
            ])
            ->add('shortDescription',TextType::class, [
                'label'=>'Courte description', 
                'attr'=>[
                    'placeholder'=>'Quelques mots pour comprendre rapidement'
                ]
            ])
            ->add('content', TextareaType::class, [
                'label'=>'Description', 
                'attr'=>[
                    'placeholder'=>'Décrivez ici la figure. Les points de difficultés, ...  ;)'
                ]
            ])
            ->add('picturesVisuel', FileType::class, [
                'mapped'=> false,
                'label'=>"Ajouter des photos",
                'required'=>false,
                'multiple'=> true,
                'attr'=>[
                    'accept'=> ".jpg,.jpeg,.png", 
                    'max-size'=> 2000
                ]
            ])
            ->add('videosVisuels', TextareaType::Class, [
                'label'=>"Ajouter des vidéos",
                'mapped'=>false, 
                'attr'=>[
                    'placeholder'=>'Vos vidéos'
                ], 
                'required'=>false
            ])
            ->add('mainVisuelSelect', null, [
                'mapped'=>false,
                'label'=>false, 
                'required'=>false
            ])
            ->add('Enregistrer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,
        ]);
    }
}
