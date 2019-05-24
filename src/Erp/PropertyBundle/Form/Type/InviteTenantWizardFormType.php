<?php
namespace Erp\PropertyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class InviteTenantFormType
 *
 * @package Erp\PropertyBundle\Form\Type
 */
class InviteTenantWizardFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'invitedEmail',
            'email',
            [
                'attr'       => ['placeholder' => 'Email'],
                'label'      => false,
                'required'   => true,
            ]
        );
        $builder->add(
            'firstName',
            null,
            [
                'attr'       => ['placeholder' => 'First Name'],
                'label'      => false,
                'empty_data' => 'First Name',
                'required'   => true,
            ]
        );
        $builder->add(
            'lastName',
            null,
            [
                'attr'       => ['placeholder' => 'Last Name'],
                'label_attr' => ['class' => 'control-label required-label'],
                'required'   => true,
                'empty_data' => 'Last Name'
            ]
        );
        $builder->add(
            'birthdate',
            'birthday',
            [
                'widget' => 'single_text',
                'label'      => false,
                'placeholder' => 'Birthday',
                // this is actually the default format for single_text
                //'widget' => 'single_text',
                'format' => 'MM/dd/yyyy'
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
                'data_class'        => 'Erp\UserBundle\Entity\InvitedUser',
                'validation_groups' => ['InvitedUser']
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
        return 'erp_invite_user_form';
    }
}
