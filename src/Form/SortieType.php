<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('debut', DateTimeType::class)
            ->add('duree', IntegerType::class,[
                'attr'=>[
                    'class'=>'duree',
                ]
            ])
            ->add('clotureInscriptions')
            ->add('inscriptionsMax')
            ->add('description', TextareaType::class, array(
                'attr' => array('maxlength' => 500)))
            ->add('urlPhoto', FileType::class, array('label' => 'Ajouter une photo',  'mapped' => false, 'required' => false,  'constraints' => [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'image/png',
                        'image/jpeg',
                        'image/jpg',
                    ],
                    'mimeTypesMessage' => 'Format invalide',
                ])
            ], ))
            ->add('ville', EntityType::class, [
                'class'=>Ville::class,
                'mapped'=>false,
                'placeholder'=>'Choisissez une ville',
                'attr'=>[
                    'class'=>'listeVille',
                ],

            ])
            ->add('lieu', EntityType::class, [
                'class'=>Lieu::class,
                'placeholder'=>'Choisissez un lieu',
            ])
            ->add('submit', SubmitType::class, [
                'label'=>'Valider'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }





}
