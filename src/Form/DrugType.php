<?php

namespace App\Form;

use App\Entity\Drug;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;

class DrugType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('milligram')
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Comprimé' => 'com',
                    'Gélule' => 'gel'
                ],
            ])
            ->add('package')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Drug::class,
        ]);
    }
}
