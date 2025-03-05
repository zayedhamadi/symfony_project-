<?php

namespace App\Form;

use App\Entity\Matiere;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

class MatiereType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Subject Name',
                'attr' => ['class' => 'form-control'],


            ])
            ->add('coefficient', IntegerType::class, [
                'label' => 'Coefficient',
                'attr' => ['class' => 'form-control'],

            ])
            ->add('idEnsg', EntityType::class, [
                'class' => User::class,  // User is the entity for teacher
                'choice_label' => 'nom', // or the appropriate property to display the teacher name
                'placeholder' => 'Choose an enseignant',
                'multiple' => false,  // This ensures only one teacher is selected
                'expanded' => false,  // This creates a dropdown (select box)
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.roles LIKE :role')
                        ->setParameter('role', '%Enseignant%');
                },
            ]);
//            ->add('enseignants', EntityType::class, [
//                'class' => User::class,
//                'choice_label' => 'nom', // Display teacher's name
//                'multiple' => true,  // Allow multiple selections
//                'expanded' => false, // Use a dropdown
//                'label' => 'Select Teachers',
//                'attr' => ['class' => 'form-control'],
//            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Matiere::class,
        ]);
        $resolver->setDefined(['matiere_id']);
    }
}