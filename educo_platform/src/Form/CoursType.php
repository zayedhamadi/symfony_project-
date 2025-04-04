<?php
namespace App\Form;

use App\Entity\Classe;
use App\Entity\Cours;
use App\Entity\Matiere;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
                'choices' => $options['matieres'], // Ensure this matches the key passed from the controller
                'choice_label' => 'nom',
                'placeholder' => 'Select a Matiere',
                'attr' => ['class' => 'form-control'],
                'required' => true,
            ])
            ->add('pdfFile', FileType::class, [
                'label' => 'Upload PDF',
                'mapped' => false, // Not mapped to entity property
                'required' => false,
//                'constraints' => [
//                    new File([
//                        'maxSize' => '5M',
//                        'mimeTypes' => ['application/pdf'],
//                        'mimeTypesMessage' => 'Please upload a valid PDF file.',
//                    ])
//                ],
//                'attr' => ['class' => 'form-control'],

            ])
            ->add('classe', EntityType::class, [
                'class' => Classe::class,
                'choice_label' => 'nom_classe', // Show class name in the dropdown
                'label' => 'Select Class',
                'attr' => ['class' => 'form-control']
            ]);
        $builder
            ->add('chapterNumber', IntegerType::class, [
                'label' => 'Chapter Number',
                'attr' => ['class' => 'form-control', 'min' => 1],
                'required' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cours::class,
            'matieres' => [], // Define the matieres option
        ]);
    }
}