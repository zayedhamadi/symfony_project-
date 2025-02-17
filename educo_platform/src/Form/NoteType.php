<?php

namespace App\Form;

use App\Entity\Eleve;
use App\Entity\Note;
use App\Entity\Quiz;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('score')
            ->add('eleve', EntityType::class, [
                'class' => Eleve::class,
                'choice_label' => 'nom', // Change 'name' to the actual property that holds the student's name
                'placeholder' => 'Select an Élève',
            ])
            ->add('quiz', EntityType::class, [
                'class' => Quiz::class,
                'choice_label' => 'titre', // Change 'title' to the actual property that holds the quiz title
                'placeholder' => 'Select a Quiz',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
    }
}
