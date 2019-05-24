<?php

namespace Erp\PropertyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ContractSectionFormType
 *
 * @package Erp\PropertyBundle\Form\Type
 */
class ContractSectionFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'submit',
            'submit',
            ['label' => '+ Add Section', 'attr' => ['class' => 'btn-circle']]
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Erp\PropertyBundle\Entity\ContractSection',
            'validation_groups' => ['ContractSection']
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'erp_propertybundle_contract_section';
    }
}
