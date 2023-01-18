<?php

namespace App\Form;

use App\DTO\UpdateUser;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    1 => 'ROLE_USER',
                    2 => 'ROLE_EDITOR',
                    3 => 'ROLE_ADMIN'
                ],
                'error_bubbling'=>true,
            ])
            ->add('token', TextType::class,[
                'mapped'=>false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UpdateUser::class,
            'csrf_protection' => false,
        ]);
    }
}
