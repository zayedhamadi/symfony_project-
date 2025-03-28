<?php

namespace App\Form;

use App\Entity\Eleve;
use App\Entity\InscriptionEvenement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Gregwar\CaptchaBundle\Type\CaptchaType;

class InscriptionEvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('enfant', EntityType::class, [
                'class' => Eleve::class,
                'choices' => $options['enfants'], // Liste des enfants du parent
                'choice_label' => 'nom', // Afficher le nom de l'enfant
                'label' => 'Sélectionnez l\'enfant',
            ])
            ->add('captcha', CaptchaType::class, [
                'label' => 'Captcha',
                'width' => 200,
                'height' => 50,
                'length' => 6,
                
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InscriptionEvenement::class,
            'enfants' => [] // Option pour passer les enfants dynamiquement
        ]);
    }
}
