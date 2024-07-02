<?php

namespace App\Form;

use App\Entity\Campus;
use App\Form\Model\OutingsFilter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OutingsFilterType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
        ->add('campusChoice', EntityType::class, [
            'class' => Campus::class,
            'choice_label' => 'name',
            'label' => 'Campus',
            'placeholder' => 'Filtrer par campus'
        ])
        ->add('titleSearch', SearchType::class, [
            'label' => 'Le nom de la sortie contient'
        ])
        ->add('startDate', DateType::class, [
            'widget' => 'single_text',
            'label' => 'Entre',
            'row_attr' => [
                'class' => 'space-x-4'
            ]
        ])
        ->add('endDate', DateType::class, [
            'widget' => 'single_text',
            'label' => 'et',
            'row_attr' => [
                'class' => 'space-x-4'
            ]
        ])
        ->add('isHost', CheckboxType::class, [
            'label' => 'Sorties dont je suis l\'organisateur/trice',
            'label_attr' => [
                'class' => 'cursor-pointer'
            ]
        ])
        ->add('isEntered', CheckboxType::class, [
            'label' => 'Sorties auxquelles je suis inscrit/e',
            'label_attr' => [
                'class' => 'cursor-pointer'
            ]
        ])
        ->add('isNotEntered', CheckboxType::class, [
            'label' => 'Sorties auxquelles je ne suis pas inscrit/e',
            'label_attr' => [
                'class' => 'cursor-pointer'
            ]
        ])
        ->add('isPast', CheckboxType::class, [
            'label' => 'Sorties passées',
            'label_attr' => [
                'class' => 'cursor-pointer'
            ]
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Rechercher'
        ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
        'data_class' => OutingsFilter::class,
        'required' => false
    ]);
  }
}
