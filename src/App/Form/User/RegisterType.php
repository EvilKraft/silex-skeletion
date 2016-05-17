<?php

namespace App\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, array(
                'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 3))),
                'attr' => array('class' => 'form-control', 'placeholder' => 'Username'),
            ))
            ->add('email', EmailType::class, array(
                'constraints' => array(new Assert\NotBlank(), new Assert\Email()),
                'attr' => array('class' => 'form-control', 'placeholder' => 'user@email.com'),
            ))
            ->add('password', RepeatedType::class, array(
                'type'            => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options'         => array(
                    'attr' => array('class' => 'form-control'),
                ),
                'first_options'   => array('label' => 'Password'),
                'second_options'  => array('label' => 'Repeat Password'),
                'required'        => false,
            ))

        ;
    }

    public function getName()
    {
        return 'user_register';
    }
}
