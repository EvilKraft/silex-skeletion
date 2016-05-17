<?php

namespace App\Form\Admin;

use Silex\Application;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class GroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('id', HiddenType::class, array(
                'required'  => false,
                'mapped' => false,
            ))
            ->add('name', TextType::class, array(
                'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 3))),
                'attr' => array('class' => 'form-control', 'placeholder' => 'Name'),
            ))
            ->add('title', TextType::class, array(
                'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 3))),
                'attr' => array('class' => 'form-control', 'placeholder' => 'Displayed Title'),
            ))
            ->add('roles', ChoiceType::class, array(
                'choices' => $this->getAllRoles($options['controllers']),
                'expanded' => true,
                'multiple'  => true,
            ))
        ;
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if(!is_null($view->vars['data']->getId())){
            $view->children['name']->vars['attr']['readonly'] = 'readonly';
        }
    }

    public function getName()
    {
        return 'group';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Groups',
            'controllers'  => array(),
        ));
    }


    private function getAllRoles(Array $controllers){
        $roles = array();

        foreach($controllers as $controller){
            $roles[$controller['title']] = array_flip($controller['roles']);
        }

        return $roles;
    }
}
