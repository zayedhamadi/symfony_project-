<?php  
namespace App\Form;  

use Symfony\Component\Form\Extension\Core\Type\TextType;  
use App\Entity\Classe;  
use App\Entity\User;  
use Symfony\Bridge\Doctrine\Form\Type\EntityType;  
use Symfony\Component\Form\AbstractType;  
use Symfony\Component\Form\FormBuilderInterface;  
use Symfony\Component\OptionsResolver\OptionsResolver;  
use Doctrine\ORM\EntityRepository;  

class ClasseType extends AbstractType  
{  
    public function buildForm(FormBuilderInterface $builder, array $options): void  
    {  
        $builder  
            ->add('nom_classe', TextType::class)  
            ->add('Num_salle', TextType::class)  
            ->add('capacite_max', TextType::class)  
            ->add('id_user', EntityType::class, [  
                'class' => User::class,  
                'choice_label' => 'email',  
                'multiple' => true,  
                'query_builder' => function (EntityRepository $er) {  
                    return $er->createQueryBuilder('u')  
                        ->where('u.roles LIKE :role')  
                        ->setParameter('role', '%"Enseignant"%');  
                },  
                'placeholder' => 'Choisir un parent',  
            ]);  
    }  

    public function configureOptions(OptionsResolver $resolver): void  
    {  
        $resolver->setDefaults([  
            'data_class' => Classe::class,  
        ]);  
    }  
}
