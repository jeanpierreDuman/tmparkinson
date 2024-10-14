<?php

namespace App\Form;

use App\Entity\Drug;
use App\Entity\Prescription;
use App\Entity\PrescriptionLine;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrescriptionLinesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('drug', EntityType::class, [
                'class' => Drug::class,
            ])
            ->add('quantity')
            ->add('frequency', ChoiceType::class, [
                'choices' => [
                    'Matin' => 'morning',
                    'Midi' => 'noon',
                    'Soir' => 'night'
                ],
                'multiple' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PrescriptionLine::class,
        ]);
    }
}
