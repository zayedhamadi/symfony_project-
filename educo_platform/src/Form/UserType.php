<?php

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Enum\Rolee;
use Symfony\Component\Form\FormBuilderInterface;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('role', ChoiceType::class, [
            'choices'  => [
                'Admin' => Rolee::Admin,
                'Enseignant' => Rolee::Enseignant,
                'Parent' => Rolee::Parent,
            ],
            'expanded' => false, // Set to true for radio buttons
            'multiple' => false, // Only allow one selection
        ]);
    }
}

