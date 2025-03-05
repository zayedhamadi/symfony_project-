<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Enum\EtatCompte;
use App\Entity\Enum\Rolee;
use App\Entity\Enum\Genre;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\DateType;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', FileType::class, [
                'label' => 'Image de profil',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG, PNG, GIF)',
                    ])
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => false,
                'attr' => ['placeholder' => 'Entrez votre mot de passe']
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
                'attr' => ['placeholder' => 'Entrez votre adresse e-mail']
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Entrez votre nom']
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Entrez votre prénom']
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'attr' => ['placeholder' => 'Entrez votre adresse']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['placeholder' => 'Décrivez-vous en quelques mots']
            ])
            ->add('num_tel', TelType::class, [
                'label' => 'Numéro de téléphone',
                'attr' => ['placeholder' => 'Entrez votre numéro de téléphone']
            ])
            ->add('dateNaissance', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'attr' => ['placeholder' => 'Entrez votre date de naissance']
            ])
            ->add('genre', ChoiceType::class, [
                'choices' => Genre::cases(),
                'choice_label' => fn(Genre $choice) => $choice->name,
                'choice_value' => fn(?Genre $choice) => $choice?->value,
                'label' => 'Genre',
                'placeholder' => 'Sélectionnez votre genre'
            ]);
//            ->add('etatCompte', ChoiceType::class, [
//                'choices' => EtatCompte::cases(),
//                'choice_label' => fn(EtatCompte $choice) => $choice->name,
//                'choice_value' => fn(?EtatCompte $choice) => $choice?->value,
//                'label' => 'État du compte',
//                'placeholder' => 'Sélectionnez un état'
//            ])
//            ->add('roles', ChoiceType::class, [
//                'choices' => array_combine(
//                    array_map(fn(Rolee $role) => $role->name, array_filter(Rolee::cases(), fn(Rolee $role) => $role !== Rolee::Admin)),
//                    array_map(fn(Rolee $role) => $role->value, array_filter(Rolee::cases(), fn(Rolee $role) => $role !== Rolee::Admin))
//                ),
//                'choice_label' => fn($choice) => $choice,
//                'choice_value' => fn($choice) => (string)$choice,
//                'label' => 'Roles',
//                'multiple' => true,
//                'expanded' => true
//            ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
