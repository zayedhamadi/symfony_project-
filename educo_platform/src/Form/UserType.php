<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Enum\EtatCompte;
use App\Entity\Enum\Rolee;
use App\Entity\Enum\Genre;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
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
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\GreaterThan;

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
                'attr' => ['placeholder' => 'Entrez votre mot de passe'],
                'constraints' => [
                    new NotBlank(['message' => 'Le mot de passe est obligatoire']),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
                'attr' => ['placeholder' => 'Entrez votre adresse e-mail'],
                'constraints' => [
                    new NotBlank(['message' => 'L\'e-mail est obligatoire']),
                    new Email(['message' => 'Veuillez entrer une adresse e-mail valide']),
                ]
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Entrez votre nom'],
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est obligatoire']),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères',
                    ])
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Entrez votre prénom'],
                'constraints' => [
                    new NotBlank(['message' => 'Le prénom est obligatoire']),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le prénom doit contenir au moins {{ limit }} caractères',
                    ])
                ]
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'attr' => ['placeholder' => 'Entrez votre adresse'],
                'constraints' => [
                    new NotBlank(['message' => 'L\'adresse est obligatoire']),
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['placeholder' => 'Décrivez-vous en quelques mots'],
                'constraints' => [
                    new NotBlank(['message' => 'La description est obligatoire']),
                    new Length([
                        'max' => 500,
                        'maxMessage' => 'La description ne doit pas dépasser {{ limit }} caractères',
                    ])
                ]
            ])
            ->add('num_tel', TelType::class, [
                'label' => 'Numéro de téléphone',
                'attr' => ['placeholder' => 'Entrez votre numéro de téléphone'],
                'constraints' => [
                    new NotBlank(['message' => 'Le numéro de téléphone est obligatoire']),
                    new Length([
                        'min' => 8,
                        'max'=>8,
                        'minMessage' => 'Le numéro de téléphone doit contenir au moins {{ limit }} caractères',
                    ])
                ]
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
                'placeholder' => 'Sélectionnez votre genre',
                'constraints' => [
                    new NotBlank(['message' => 'Le genre est obligatoire']),
                ]
            ])
            ->add('etatCompte', ChoiceType::class, [
                'choices' => EtatCompte::cases(),
                'choice_label' => fn(EtatCompte $choice) => $choice->name,
                'choice_value' => fn(?EtatCompte $choice) => $choice?->value,
                'label' => 'État du compte',
                'placeholder' => 'Sélectionnez un état',
                'constraints' => [
                    new NotBlank(['message' => 'L\'état du compte est obligatoire']),
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => array_combine(
                    array_map(fn(Rolee $role) => $role->name, Rolee::cases()),
                    array_map(fn(Rolee $role) => $role->value, Rolee::cases())
                ),
                'choice_label' => fn($choice) => $choice,
                'choice_value' => fn($choice) => (string)$choice,
                'label' => 'Roles',
                'multiple' => true,
                'expanded' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le rôle est obligatoire']),
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
