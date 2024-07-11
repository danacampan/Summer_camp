<?php

namespace App\Form\Type;

use App\Entity\Exercise;
use App\Entity\ExerciseLog;

use App\Entity\Workout;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            ->add('workout', ChoiceType::class, [
                'choices' => $options['workouts'],
                'choice_label' => function (Workout $workout) {
                    return $workout->getName();
                },
                'label' => 'Selectează antrenamentul',
            ])
            ->add('Exercise', EntityType::class, array(
                'class' => 'App\Entity\Exercise',
                'choice_label' => 'nume',
                'label' => 'Selecteaza exercitiul',
            ), )
            ->add('nr_reps', IntegerType::class,[
                'label' => 'Numar repetari',
            ])
            ->add('durata', TimeType::class,[
                'label' => 'Durata  (Minute : Secunde)',
            ])
            ->add('save', SubmitType::class);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExerciseLog::class,
            'workouts' => Workout::class,
        ]);
    }
}