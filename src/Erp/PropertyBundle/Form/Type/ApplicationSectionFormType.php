<?php

namespace Erp\PropertyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ApplicationSectionFormType
 *
 * @package Erp\PropertyBundle\Form\Type
 */
class ApplicationSectionFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            'text',
            ['label' => false, 'attr' => ['class' => 'form-control section-footer-input']]
        );

        $builder->add(
            'submit',
            'submit',
            ['label' => 'Add', 'attr' => ['class' => 'btn submit-popup-btn btn-footer']]
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Erp\PropertyBundle\Entity\ApplicationSection',
            'validation_groups' => ['ApplicationSection']
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'erp_propertybundle_application_section';
    }
}
