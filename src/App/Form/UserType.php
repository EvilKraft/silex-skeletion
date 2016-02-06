<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', array(
                'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 3))),
                'attr' => array('class' => 'form-control', 'placeholder' => 'Username'),
            ))
            ->add('password', 'repeated', array(
                'type'            => 'password',
                'invalid_message' => 'The password fields must match.',
                'options'         => array(
                    'attr' => array('class' => 'form-control'),
                ),
                'first_options'   => array('label' => 'Password'),
                'second_options'  => array('label' => 'Repeat Password'),
                'required'        => false,
            ))
            ->add('mail', 'email', array(
                'constraints' => array(new Assert\NotBlank(), new Assert\Email()),
                'attr' => array('class' => 'form-control', 'placeholder' => 'user@email.com'),
            ))
            ->add('role', 'choice', array(
                'constraints' => array(new Assert\NotBlank()),
                'choices' => array('ROLE_USER' => 'User', 'ROLE_ADMIN' => 'Admin'),
                'attr' => array('class' => 'form-control'),
                'placeholder' => 'Choose a role',
            ))
            ->add('status', 'choice', array(
                'choices' => array('1' => 'Active', '0' => 'Inactive'),
                'attr' => array('class' => 'zzzz'),
                'expanded' => true,
            ));
    }

    public function getName()
    {
        return 'user';
    }
}
