<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('oldPlainPassword', PasswordType::class,[
                'mapped' => false,
                'required'=>true,
                'attr'=>['placeholder' => 'Old Password']
            ])
            ->add('newPlainPassword', RepeatedType::class,[
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'password.match',
                'required' => true,
                'error_bubbling' =>true,
                'first_options' => ['label' => false ],
                'second_options' => ['label' => false ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'password.not.blank',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'password.min.length',
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
