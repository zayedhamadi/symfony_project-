<?php  

namespace App\Form;  

use App\Entity\Classe;  
use App\Entity\Eleve;  
use App\Entity\User;  
use Symfony\Bridge\Doctrine\Form\Type\EntityType;  
use Symfony\Component\Form\AbstractType;  
use Symfony\Component\Form\FormBuilderInterface;  
use Symfony\Component\OptionsResolver\OptionsResolver;  
use Doctrine\ORM\EntityRepository;  

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
            ->add('DateInscription', null, [  
                'widget' => 'single_text',  
            ])  
            ->add('IdClasse', EntityType::class, [  
                'class' => Classe::class,  
                'choice_label' => 'nomClasse', 
                'placeholder' => 'Sélectionnez une classe',  
                'required' => true,  
            ])  
            ->add('IdParent', EntityType::class, [  
                'class' => User::class,  
                'choice_label' => 'email',  
                'placeholder' => 'Sélectionnez une parent',  
                'required' => true,  
                'query_builder' => function (EntityRepository $er) {  
                    return $er->createQueryBuilder('u')  
                        ->where('u.roles LIKE :role')  
                        ->setParameter('role', '%"Parent"%');  
                },  
            ]);  
    }  

    public function configureOptions(OptionsResolver $resolver): void  
    {  
        $resolver->setDefaults([  
            'data_class' => Eleve::class,  
        ]);  
    }  
}
