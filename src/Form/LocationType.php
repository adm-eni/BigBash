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
        ->add('name', null, [
            'label' => 'Nom',
        ])
        ->add('street', null, [
            'required' => false,
            'label' => 'Rue',
        ])
        ->add('latitude', null, [
            'label' => 'Latitude',
            'required' => false,
        ])
        ->add('longitude', null, [
            'label' => 'Longitude',
            'required' => false,
        ])
        ->add('city', EntityType::class, [
            'label' => 'Ville',
            'class' => City::class,
            'choice_label' => 'name',
            'required' => false,
            'placeholder' => 'Sélectionner la ville',
        ])
        ->add('create', SubmitType::class, [
            'label' => 'Envoyer',
        ])
        ->add('cancel', SubmitType::class, [
            'label' => 'Annuler',
            'attr' => [
                'formnovalidate' => 'formnovalidate',
            ]
        ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
        'data_class' => Location::class,
    ]);
  }
}
