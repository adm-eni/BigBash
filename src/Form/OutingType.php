<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Location;
use App\Entity\Outing;
use App\Entity\Status;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OutingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'required' => true,
                'label' => 'Titre',
            ])
            ->add('startAt', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
                'label' => 'Date et heure de la sortie',
            ])
            ->add('entryDeadline', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'label' => 'Date limite d\'inscription',
            ])
            ->add('maxEntryCount', null, [
                'required' => false,
                'label' => 'Nombre de places',
            ])
            ->add('duration', null, [
                'required' => false,
                'label' => 'Durée',
            ])
            ->add('description', null, [
                'required' => false,
            ])

            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'required' => false,
            ])
            ->add('location', EntityType::class, [
                'class' => Location::class,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'required' => true,
                'label' => 'Lieu'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
            ])
            ->add('create', SubmitType::class, [
                'label' => 'Publier',
            ])
            ->add('cancel', SubmitType::class, [
                'label' => 'Annuler',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Outing::class,
        ]);
    }
}
