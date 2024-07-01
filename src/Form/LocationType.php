<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Location;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('street', null, [
                'required' => false,
            ])
            ->add('latitude', null, [
                'required' => false,
                ])
            ->add('longitude', null, [
                'required' => false,
            ])
            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name',
                'required' => false,
            ])
            ->add('create', SubmitType::class, [
                'label' => 'Créer',
            ])
            ->add('cancel', SubmitType::class, [
                'label' => 'Annuler',
                'attr' => [
                    'formnovalidate' => 'formnovalidate',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}
