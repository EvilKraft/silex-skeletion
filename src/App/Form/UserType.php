<?php

namespace App\Form;

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


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class, array(
                'required'  => false,
                'mapped' => false,
            ))
            ->add('username', TextType::class, array(
                'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 3))),
                'attr' => array('class' => 'form-control', 'placeholder' => 'Username'),
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
            ->add('email', EmailType::class, array(
                'constraints' => array(new Assert\NotBlank(), new Assert\Email()),
                'attr' => array('class' => 'form-control', 'placeholder' => 'user@email.com'),
            ))

            ->add('groups', EntityType::class, array(
                'class' => 'App\\Entity\\Groups',

                'multiple' => true,

                'label' => 'Groups',
                'choice_label' => function ($item) {
                    return $item->getTitle();
                },
                'placeholder' => false,

                'query_builder' => function(\Doctrine\ORM\EntityRepository $repo){
                    return $repo->createQueryBuilder('node')
                        ->orderBy('node.title', 'ASC');
                },
                'attr' => array('class' => 'form-control'),
            ))


/*
            ->add('roles', ChoiceType::class, array(
                'constraints' => array(new Assert\NotBlank()),
                'choices' => array('User' => 'ROLE_USER', 'Admin' => 'ROLE_ADMIN'),
                'attr' => array('class' => 'form-control select2'),
                'placeholder' => 'Choose a role',

                'multiple' => true,

                'empty_data'  =>  'User',
            ))
*/
            ->add('image', FileType::class,  array(
                'data_class' => null,
                'attr' => array('accept' => 'image/png,image/jpeg,image/gif'),
                'constraints' => array(new Assert\Image(array(
                    'maxWidth'  => 400,
                    'maxHeight' => 400,
                    'maxSize'   => '200k',

                    'mimeTypes' => array('image/png', 'image/jpeg', 'image/gif'),

                    'maxSizeMessage'   => 'The file is too large ({{ size }} {{ suffix }}). Allowed maximum size is {{ limit }} {{ suffix }}',
                    'mimeTypesMessage' => 'The mime type of the file is invalid ({{ type }}). Allowed mime types are {{ types }}',
                ))),
                'required'   => false,
            ))
            ->add('is_active', ChoiceType::class, array(
                'choices' => array('Active' => '1', 'Inactive' => '0'),
                'expanded' => true,
            ));
    }

    public function getName()
    {
        return 'user';
    }
}
