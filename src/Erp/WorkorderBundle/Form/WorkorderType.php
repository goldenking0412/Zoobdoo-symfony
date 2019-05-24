<?php

namespace Erp\WorkorderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class WorkorderType extends AbstractType {

    protected $manager;
    protected $vendor_list;

    public function __construct($manager = null, $vendor_list = array()) {
        $this->manager = $manager;
        $this->vendor_list = $vendor_list;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('createdDate', \Symfony\Component\Form\Extension\Core\Type\DateTimeType::class, [
                    'required' => true,
                    'label' => 'Reported Date',
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'attr' => array('placeholder' => 'MM/dd/yyyy',)
                ])
                ->add('status', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, [
                    'label' => 'Status',
                    'attr' => [
                        'class' => 'form-control select-control',
                        'aria-labelledby' => 'dLabel',
                        'data-class' => 'states'
                    ],
                    'choices' => \Erp\WorkorderBundle\Entity\Workorder::getStatusOptions(),
                    'multiple' => false,
                    'required' => true
                ])
                ->add('currency', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, [
                    'label' => 'Currency',
                    'attr' => [
                        'class' => 'form-control select-control',
                        'aria-labelledby' => 'dLabel',
                        'data-class' => 'states'
                    ],
                    'choices' => \Erp\WorkorderBundle\Entity\Workorder::getCurrencyOptions(),
                    'multiple' => false,
                    'required' => true
                ])
                ->add('contractor', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, [
                    'label' => 'Contractor',
                    'attr' => [
                        'class' => 'form-control select-control select2-show-search-box',
                    ],
                    'choices' => $this->getVendorList(),
                    'required' => true
                ])
                ->add('manager', HiddenType::class, array('data' => $this->manager))
                ->add('severity', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, [
                    'label' => 'Severity',
                    'attr' => [
                        'class' => 'form-control select-control',
                        'aria-labelledby' => 'dLabel',
                        'data-class' => 'states'
                    ],
                    'choices' => \Erp\WorkorderBundle\Entity\Workorder::getSeverityOptions(),
                    'multiple' => false,
                    'required' => true
                ])
                ->add('urgency', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, [
                    'label' => 'Urgency',
                    'attr' => [
                        'class' => 'form-control select-control',
                        'aria-labelledby' => 'dLabel',
                        'data-class' => 'states'
                    ],
                    'choices' => \Erp\WorkorderBundle\Entity\Workorder::getPriorityOptions(),
                    'multiple' => false,
                    'required' => true
                ])
                ->add('description', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, [
                    'label' => 'Description',
                    'attr' => [
                        'class' => 'full-width form-control',
                        'placeholder' => 'Problem Description',
                        'maxlength' => 512,
                    ],
                    'required' => true,
                ])
                ->add('serviceDate', \Symfony\Component\Form\Extension\Core\Type\DateType::class, [
                    'required' => true,
                    'label' => 'Service Date',
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'attr' => [
                        'placeholder' => 'MM/dd/yyyy',
                    ]
                ])
                ->add('serviceTime', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                    'label' => 'Service Time',
                    'required' => false,
                    'attr' => ['class' => 'form-control', 'placeholder' => 'hh:mm', 'pattern' => '.{2,6}']
                ])
                ->add('save', 'submit', ['label' => 'Save', 'attr' => ['class' => 'btn-circle', 'value' => 'Next']])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => \Erp\WorkorderBundle\Entity\Workorder::class
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'erp_workorderbundle_edit';
    }

    public function getVendorList() {
        return $this->vendor_list;
    }

}
