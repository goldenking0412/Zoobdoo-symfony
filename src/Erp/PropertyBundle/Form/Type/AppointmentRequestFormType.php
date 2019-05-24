<?php
namespace Erp\PropertyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AppointmentRequestFormType
 *
 * @package Erp\PropertyBundle\Form\Type
 */
class AppointmentRequestFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'userName',
            'text',
            [
                'attr'       => ['class' => 'form-control'],
                'label'      => 'Name',
                'label_attr' => ['class' => 'control-label required-label'],
                'required'   => true,
            ]
        );
        $builder->add(
            'phone',
            'text',
            [
                'attr'       => ['class' => 'form-control'],
                'label'      => 'Phone',
                'label_attr' => ['class' => 'control-label'],
                'required'   => false,
            ]
        );
        $builder->add(
            'email',
            'text',
            [
                'attr'       => ['class' => 'form-control contact-email'],
                'label'      => 'Email',
                'label_attr' => ['class' => 'control-label required-label '],
                'required'   => true,
            ]
        );
        $builder->add(
            'subject',
            'text',
            [
                'attr'       => ['class' => 'form-control subject full-width'],
                'label'      => 'Subject',
                'label_attr' => ['class' => 'control-label required-label '],
                'required'   => true,
            ]
        );
        $builder->add(
            'message',
            null,
            [
                'attr'       => ['class' => 'form-control full-width'],
                'label'      => 'Message',
                'label_attr' => ['class' => 'control-label required-label'],
                'required'   => true,
            ]
        );
        $builder->add(
            'captcha',
            'captcha',
            [
                'width'  => 200,
                'height' => 70,
                'length' => 4,
                'label'  => 'Security Check',
                'attr'   => ['class' => 'form-control captcha-input'],
            ]
        );
        $builder->add(
            'submit',
            'submit',
            ['label' => 'Submit', 'attr' => ['class' => 'btn-circle captcha-submit']]
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
                'data_class'        => 'Erp\PropertyBundle\Entity\AppointmentRequest',
                'validation_groups' => ['RequestCreated']
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
        return 'erp_appointment_request_form';
    }
}
