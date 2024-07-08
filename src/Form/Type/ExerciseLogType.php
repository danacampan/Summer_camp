<?php

namespace App\Form\Type;

use App\Entity\Exercise;
use App\Entity\ExerciseLog;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ExerciseLogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Workout', EntityType::class, array(
                'class' => 'App\Entity\Workout',
                'choice_label' => 'Name',
            ), )
            ->add('Exercise', EntityType::class, array(
                'class' => 'App\Entity\Exercise',
                'choice_label' => 'nume',
            ), )
            ->add('nr_reps', IntegerType::class)
            ->add('durata', TimeType::class,)
            ->add('save', SubmitType::class);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExerciseLog::class,
        ]);
    }
}