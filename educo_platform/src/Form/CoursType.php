<?php
namespace App\Form;

use App\Entity\Cours;
use App\Entity\Matiere;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class CoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Course Name',
            ])
            ->add('IdMatiere', EntityType::class, [
                'class' => Matiere::class,
                'choice_label' => 'nom', // Replace 'nom' with the correct property of Matiere
                'placeholder' => 'Select a Matiere',
                'attr' => ['class' => 'form-control'],
                'required' => true,
            ])
            ->add('pdfFile', FileType::class, [
                'label' => 'Upload PDF',
                'mapped' => false, // Not mapped to entity property
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['application/pdf'],
                        'mimeTypesMessage' => 'Please upload a valid PDF file.',
                    ])
                ],
                'attr' => ['class' => 'form-control'],

            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cours::class,
        ]);
    }
}