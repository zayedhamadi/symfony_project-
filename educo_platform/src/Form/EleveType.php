<?php

namespace App\Form;

use App\Entity\Classe;
use App\Entity\Eleve;
use App\Entity\Matiere;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EleveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Nom')
            ->add('Prenom')
            ->add('DateDeNaissance', null, [
                'widget' => 'single_text',
            ])
            ->add('moyenne')
            ->add('NbreAbscence')
            ->add('IdClasse', EntityType::class, [
                'class' => Classe::class,
                'choice_label' => 'id',
            ])
            ->add('IdParent', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('matieres', EntityType::class, [
                'class' => Matiere::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Eleve::class,
        ]);
    }
}
