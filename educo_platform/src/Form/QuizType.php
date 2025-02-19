<?php

namespace App\Form;

use App\Entity\Cours;
use App\Entity\Quiz;
use App\Entity\Classe;
use App\Entity\Matiere;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;


class QuizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre du quiz',
                'attr' => ['placeholder' => 'Saisir le titre du quiz'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['placeholder' => 'Décrivez le quiz...'],
            ])
            ->add('cours', EntityType::class, [
                'class' => Cours::class,
                'choice_label' => 'name', // Use the correct property name for Cours
                'label' => 'Cours associé',
                'placeholder' => 'Sélectionnez un cours',
            ])
            ->add('classe', EntityType::class, [
                'class' => Classe::class,
                'choice_label' => 'nom_classe', // Use the correct property name for Classe
                'label' => 'Classe',
                'placeholder' => 'Sélectionnez une classe',
            ])
            ->add('matiere', EntityType::class, [
                'class' => Matiere::class,
                'choice_label' => 'nom', // Use the correct property name for Matiere
                'label' => 'Matière',
                'placeholder' => 'Sélectionnez une matière',
            ])
            ->add('dateAjout', DateTimeType::class, [
                'label' => 'Date d\'ajout',
                'widget' => 'single_text',
                'disabled' => true, // Empêche l'édition
                'required' => false, // Permet de ne pas exiger de valeur à l'édition
            ]);
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
        ]);
    }
}