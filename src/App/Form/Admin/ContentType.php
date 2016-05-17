<?php

namespace App\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

use App\Form\Types\LangtabsType;


class ContentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $parent = $options['data']->getParent();
        $parentId = (is_null($parent))? $parent : $parent->getId();
        $parentId = 15;

        $builder
            ->add('id', HiddenType::class, array(
                'required'  => false,
            ))
/*
            ->add('parentId1', HiddenType::class, array(
                'mapped' => false,
                'data' => $parentId,
            ))


            ->add('parentId2', ChoiceType::class, array(
                'choices' => array( 1 => 'zz1', 2 => 'zz2', 3 => 'zz3', 4 => 'zz4', 5 => 'zz5', 15 => 'zz15', 20 => 'zz20'),
                'mapped' => false,

                'placeholder' => 'Choose an option',
                'data' => $parentId,
            ))
*/
            ->add('parentId', EntityType::class, array(
                'class' => 'App\\Entity\\Content',
                'mapped' => false,
                'required'  => false,

                'label' => 'Parrent',
                'choice_label' => function ($item) {
                    return $item->getLaveledTitle();
                },
                //'choice_label' => 'translations[en].name',

        //        'placeholder' => 'Choose an option',
                'placeholder' => false,

                'query_builder' => function(\Doctrine\ORM\EntityRepository $repo){
                    return $repo->createQueryBuilder('node')
                        ->orderBy('node.root, node.lft', 'ASC');
                },
                'attr' => array('class' => 'form-control'),
                'data' => $parent,
            ))

            ->add('alias', TextType::class, array(
                'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 3))),
                'attr' => array('class' => 'form-control', 'placeholder' => 'Alias'),
            ))
            ->add('langs', LangtabsType::class, array(
                'entry_type' => ContentLangType::class,
            ))
        ;


        /*
                $builder->add('languages', 'entity', array(
                    'class' => 'App\\Entity\\Languages',
                //    'multiple' => false,
                 //   'required' => true,

                    'choice_label' => 'title',

                    'query_builder' => function(\Doctrine\ORM\EntityRepository $repo) {
                        return $repo->createQueryBuilder('r')                         ->orderBy('r.id', 'ASC');
                    },
                ));

                $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($builder) {
                    $form = $event->getForm();
                    $data = $event->getData();

                    // check if the Product object is "new"
                    // If no data is passed to the form, the data is "null".
                    // This should be considered a new "Product"
                    if (!$data || null === $data->getId()) {
                        $form->add('name', TextType::class);
                    }

                    if ($data instanceof \App\Entity\Content) {
                        $form->add('langs', 'entity', array(
                            'class' => 'App\\Entity\\ContentLangs',
                            //    'multiple' => false,
                            //   'required' => true,

                            'choice_label' => 'title',

                            'query_builder' => function(\Doctrine\ORM\EntityRepository $repo) use ($data){
                                return $repo->createQueryBuilder('r')
                                    ->where('r.content = :contentId')
                                    ->setParameter('contentId', $data->getId())
                                    ->orderBy('r.id', 'ASC');
                            },
                        ));
                    }
                });
        */

    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['langs'] = $form->getConfig()->getOption('langs');
    }


    public function getName()
    {
        return 'content';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Content',
            'langs'  => array(),
        ));
    }
}
