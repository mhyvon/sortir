<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('debut')
            ->add('duree')
            ->add('clotureInscriptions')
            ->add('inscriptionsMax')
            ->add('description')
            ->add('urlPhoto')
            ->add('organisateur')
            ->add('etat')
            ->add('inscriptions')
            ->add('lieu')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}