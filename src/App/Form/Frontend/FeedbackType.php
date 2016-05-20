<?php

namespace App\Form\Frontend;

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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class FeedbackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 3))),
                'attr' => array('class' => 'form-control', 'placeholder' => 'Name'),
            ))
            ->add('email', EmailType::class, array(
                'constraints' => array(new Assert\NotBlank(), new Assert\Email()),
                'attr' => array('class' => 'form-control', 'placeholder' => 'user@email.com'),
            ))
            ->add('message', TextareaType::class, array(
                'constraints' => array(new Assert\NotBlank()),
                'attr' => array('class' => 'form-control',),
            ))

        ;
    }

    public function getName()
    {
        return 'feedback';
    }
}
