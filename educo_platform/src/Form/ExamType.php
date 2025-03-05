<?php

// src/Form/ExamType.php
namespace App\Form;

use App\Entity\Classe;
use App\Entity\Exam;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subject', TextType::class, [
                'label' => 'Subject',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('startTime', DateTimeType::class, [
                'label' => 'Start Time',
                'widget' => 'single_text', // Use a single input for date and time
                'attr' => ['class' => 'form-control'],
            ])
            ->add('endTime', DateTimeType::class, [
                'label' => 'End Time',
                'widget' => 'single_text', // Use a single input for date and time
                'attr' => ['class' => 'form-control'],
            ])
            ->add('location', TextType::class, [
                'label' => 'Location',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('classe', EntityType::class, [
                'label' => 'Class',
                'class' => Classe::class, // The entity to use for the dropdown
                'choice_label' => 'nom_classe', // The property to display in the dropdown
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Exam::class,
        ]);
    }
}
