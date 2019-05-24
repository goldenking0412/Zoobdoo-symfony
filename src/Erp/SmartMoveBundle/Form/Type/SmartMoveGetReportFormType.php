<?php

namespace Erp\SmartMoveBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class SmartMoveExamFormType
 *
 * @package Erp\SmartMoveBundle\Form\Type
 */
class SmartMoveGetReportFormType extends AbstractType
{
    /**
     * @var array
     */
    protected $smRenters;

    /**
     * Construct method
     *
     * @param array $smRenters
     */
    public function __construct($smRenters)
    {
        $this->smRenters = $smRenters;
    }

    /**
     * Build form function
     *
     * @param FormBuilderInterface $builder the formBuilder
     * @param array                $options the options for this form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = [];
        foreach ($this->smRenters as $renter) {
            $choices[$renter->getId()] = $renter->getEmail();
        }

        $builder->add(
            'smRenters',
            'choice',
            [
                'choices'  => $choices,
                'label'    => false,
                'attr'     => ['class' => 'form-control select-control'],
                'required' => false
            ]
        )->add(
            'submit',
            'submit',
            ['label' => 'GET REPORT', 'attr' => ['class' => 'btn-circle lightbox-ajax', 'disabled' => 'disabled']]
        );
    }

    /**
     * Return unique name for this form
     *
     * @return string
     */
    public function getName()
    {
        return 'sm_get_reports_form';
    }
}
