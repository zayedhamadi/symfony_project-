<?php

namespace App\Form;

use App\Entity\Reclamation;
use App\Entity\Enum\Statut;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
       
        if (isset($options['only_statut']) && $options['only_statut'] === true) {
            $builder->add('statut', ChoiceType::class, [
                'choices' => Statut::cases(),
                'choice_label' => fn (Statut $choice) => $choice->value,
                'choice_value' => fn (?Statut $choice) => $choice?->value,
                'label' => 'Statut de la réclamation',
                'choice_translation_domain' => false,
            ]);
        } else {
            
            $builder
                ->add('titre', TextType::class, [
                    'label' => 'Titre de la réclamation',
                    'attr' => [
                        'placeholder' => 'Saisir le titre',
                    ],
                ])
                ->add('description', TextareaType::class, [
                    'label' => 'Description de la réclamation',
                    'attr' => [
                        'placeholder' => 'Décrire le problème rencontré',
                        'rows' => 5,
                    ],
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
            'only_statut' => false, // Définir 'only_statut' par défaut à false
        ]);
    }
}
