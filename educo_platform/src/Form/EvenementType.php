<?php

namespace App\Form;

use App\Entity\Evenement;
use App\Entity\Enum\EventType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('titre', TextType::class, [
            'label' => 'Titre'
        ])
        ->add('description', TextareaType::class, [
            'required' => false,
            'label' => 'Description'
        ])
            ->add('dateDebut', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de début'
            ])
            ->add('dateFin', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
                'label' => 'Date de fin'
            ])
            ->add('lieu', TextType::class, [
                'label' => 'Lieu'
            ])
            
            ->add('inscriptionRequise', CheckboxType::class, [
                'required' => false,
                'mapped' => true,
            ])
            ->add('nombrePlaces', IntegerType::class, [
                'required' => false, // ⚠️ Rendre optionnel au début
                'attr' => ['min' => 1],
                'disabled' => true, // 🔹 Désactivé par défaut
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $form = $event->getForm();
                $evenement = $event->getData();

                if ($evenement && $evenement->isInscriptionRequise()) {
                    $form->add('nombrePlaces', IntegerType::class, [
                        'required' => true,
                        'attr' => ['min' => 1],
                        'disabled' => false, // 🔹 Activer si `inscriptionRequise`
                    ]);
                }
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();

                if (isset($data['inscriptionRequise']) && $data['inscriptionRequise']) {
                    $form->add('nombrePlaces', IntegerType::class, [
                        'required' => true,
                        'attr' => ['min' => 1],
                        'disabled' => false,
                    ]);
                }
            })
            ->add('type', ChoiceType::class, [
                'choices' => EventType::cases(), // Utiliser directement les cas de l'énumération
                'choice_label' => fn (EventType $choice) => $choice->value, // Afficher la valeur de l'énumération
                'choice_value' => fn (?EventType $choice) => $choice?->value, // Utiliser la valeur de l'énumération
                'label' => 'Type d\'événement',
                'choice_translation_domain' => false, // Désactiver la traduction
            ])
;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
}
