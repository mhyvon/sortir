<?php


namespace App\Form;


use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('motR', TextType::class, [
                'required' => false,
                'label'=>'Le nom ou la description de la sortie contient',
                'attr'=>[
                  'placeholder'=>'Recherche',
                ],
            ])
            ->add('siteR', EntityType::class, [
                'class'=>Site::class,
                'label'=>'Site',
            ])
            ->add('dateD', DateType::class,[
                'required'=>false,
                'label'=>'Entre'
            ])
            ->add('dateF', DateType::class, [
                'required'=>false,
                'label'=>'et'
            ])
            ->add('orga', CheckboxType::class, [
                'required' => false,
                'label'=>'Sorties dont je suis l\'organisateur/organisatrice'
            ])
            ->add('inscr', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties auxquelles je suis inscrit(e)',
            ])
            ->add('noninscr', CheckboxType::class, [
                'required'=>false,
                'label' => 'Sorties auxquelles je ne suis pas inscrit(e)',
            ])
            ->add('passe', CheckboxType::class, [
                'required'=>false,
                'label'=>'Sorties passées',
            ])
            ->add('submit', SubmitType::class, [
                'label'=>'Rechercher',
                'attr'=>[
                    'class'=>'btn btn-primary'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'mapped'=>false,
        ]);
    }
}