<?php
namespace Erp\PropertyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class PropertySearchBarFormType
 *
 * @package Erp\PropertyBundle\Form\Type
 */

class PropertySearchBarFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'keyword',
            'text',
            [
                'constraints' => [
                    new NotBlank([
                        'groups' => ['PropertySearchBar']
                    ]),
                ],
                'label'      => false,
                'label_attr' => ['class' => 'control-label required-label'],
                'required'   => false,
            ]
        );
    }

    /**
     * Form default options
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'validation_groups' => ['PropertySearchBar']
            ]
        );
    }

    /**
     * Form name
     *
     * @return string
     */
    public function getName()
    {
        return 'erp_property_search_bar_form';
    }
}
