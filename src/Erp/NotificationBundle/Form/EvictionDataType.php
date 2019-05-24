<?php

namespace Erp\NotificationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Erp\NotificationBundle\Entity\Template;
use Erp\NotificationBundle\Form\DataTransformer\TemplateToIntTransformer;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Validator\Constraints\NotBlank;

class EvictionDataType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $templates = $options['templates'] ? $options['templates'] : [];

        $builder
                ->add('days', HiddenType::class, array(
                    'constraints' => array(new NotBlank(array(
                            'message' => 'Please enter the days after the due date for send eviction',
                            'groups' => array('Eviction')
                                )))
                ))
                ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options, $templates, $builder) {
                    $form = $event->getForm();

                    if (empty($options['rawData'])) {
                        $form
                        ->add('pickDate', DateType::class, array(
                            'label' => 'Pick the Date After Due Rent',
                            'widget' => 'single_text',
                            'html5' => false,
                            'mapped' => false,
                            'format' => 'M dd, yyyy'
                        ))
                        ->add('template', EntityType::class, array(
                            'required' => true,
                            'class' => Template::class,
                            'property' => 'title',
                            'choices' => $templates,
                            'constraints' => array(new NotBlank(array(
                                    'message' => 'Please select template from drop-down',
                                    'groups' => array('Eviction')
                            )))
                        ))
                        ;
                    } else {
                        $form
                                ->add('pickDate', HiddenType::class, array(
                                    'mapped' => false,
                                    'empty_data' => $options['rawData']['pickDate']
                                ))
                                ->add($builder->create('template', HiddenType::class, array(
                                    'empty_data' => $options['rawData']['template'],
                                    'auto_initialize' => false
                                ))->addModelTransformer(new TemplateToIntTransformer($options['em']))->getForm());
                    }
                })
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => \Erp\NotificationBundle\Entity\EvictionData::class,
            'templates' => array(),
            'rawData' => array(),
            'em' => \Doctrine\Common\Persistence\ObjectManager::class,
            'validation_groups' => ['Eviction']
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'erp_notificationbundle_evictiondata';
    }

}
