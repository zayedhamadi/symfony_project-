<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\Quiz;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
    ->add('texte', TextType::class, [
        'label' => 'Entrez la question',
        'attr' => ['class' => 'form-control'],
    ])
    ->add('options', TextType::class, [
        'label' => 'Options de réponse (séparées par des virgules)',
        'required' => true,
        'attr' => ['class' => 'form-control'],
    ])
    ->add('reponse', TextType::class, [
        'label' => 'Bonne réponse',
        'attr' => ['class' => 'form-control'],
    ]);
    
    

        // Transform the options field (JSON <-> String)
        $builder->get('options')
            ->addModelTransformer(new CallbackTransformer(
                function ($optionsArray) {
                    return is_array($optionsArray) ? implode(', ', $optionsArray) : '';
                },
                function ($optionsString) {
                    return array_map('trim', explode(',', $optionsString));
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
