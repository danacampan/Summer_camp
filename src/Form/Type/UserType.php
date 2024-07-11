<?php

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label'=>'Nume',
                ])
            ->add('parola', PasswordType::class, ['label'=>'Parola',
                ])
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'Feminin' => 1,
                    'Masculin' => 2,
                    'Prefer sa nu raspund' => 0,
                ],

            ])
            ->add('birthday', BirthdayType::class,['label'=>'Data nasterii',
               ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Utilizator'=> 'ROLE_USER',
                    'Administrator'=> 'ROLE_ADMIN',
                ],

                'label'=>'Rol','multiple' => true,
            ])
            ->add('save', SubmitType::class,['label'=>'Salveaza',]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,

        ]);

    }

}