<?php

namespace Erp\SmartMoveBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Erp\CoreBundle\Services\LocationService;

/**
 * Class SmartMovePersonalFormType
 *
 * @package Erp\SmartMoveBundle\Form\Type
 */
class SmartMovePersonalFormType extends AbstractType
{
    /**
     * @var FormBuilderInterface
     */
    protected $formBuilder;

    /**
     * @var LocationService
     */
    protected $locationService;

    /**
     * @var string
     */
    protected $email;

    /**
     * Construct method
     *
     * @param LocationService $locationService
     * @param string          $email
     */
    public function __construct(LocationService $locationService, $email)
    {
        $this->locationService = $locationService;
        $this->email = $email;
    }

    /**
     * Build form function
     *
     * @param FormBuilderInterface $builder the formBuilder
     * @param array                $options the options for this form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formBuilder = $builder;
        $this->addName('First')
            ->addName('Last')
            ->addName('Middle', false)
            ->addEmail()
            ->addDateOfBirth()
            ->addSSN()
            ->addEmploymentStatus()
            ->addAddress('One')
            ->addAddress('Two', false)
            ->addCity()
            ->addState()
            ->addZip()
            ->addMobilePhone()
            ->addHomePhone()
            ->addIncome()
            ->addIncomeFrequency()
            ->addOtherIncome()
            ->addOtherIncomeFrequency()
            ->addAssetValue()
            ->addFCRA();
    }

    /**
     * Return unique name for this form
     *
     * @return string
     */
    public function getName()
    {
        return 'sm_personal_info_form';
    }

    /**
     * @param string $name
     * @param bool   $isRequired
     *
     * @return $this
     */
    private function addName($name = 'First', $isRequired = true)
    {
        $constraints = [
            new Assert\Length(
                [
                    'min'        => $isRequired ? 2 : 1,
                    'max'        => 50,
                    'minMessage' => $name . ' Name should have minimum 2 characters and maximum 50 characters',
                    'maxMessage' => $name . ' Name should have minimum 2 characters and maximum 50 characters',
                ]
            ),
            new Assert\Regex(
                [
                    'pattern' => '/^[a-zA-Z]+$/',
                    'match'   => true,
                    'message' => ' Character Type Accepted Values: Alpha'
                ]
            ),
        ];

        $labelRequiredClass = '';
        if ($isRequired) {
            $constraints[] = new Assert\NotBlank(['message' => 'Please enter your ' . $name . ' Name']);
            $labelRequiredClass = 'required-label';
        }

        $this->formBuilder->add(
            $name . 'Name',
            'text',
            [
                'label'       => $name . ' Name',
                'label_attr'  => ['class' => 'control-label ' . $labelRequiredClass],
                'attr'        => ['class' => 'form-control', 'maxlength' => 50],
                'required'    => $isRequired,
                'constraints' => $constraints,
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addEmail()
    {
        $constraints = [
            new Assert\NotBlank(['message' => ' Email should have minimum 6 and maximum 50 characters']),
            new Assert\Length(
                [
                    'min'        => 6,
                    'max'        => 50,
                    'minMessage' => ' Email should have minimum 6 and maximum 50 characters',
                    'maxMessage' => ' Email should have minimum 6 and maximum 50 characters'
                ]
            ),
            new Assert\Email(
                ['message' => ' This value is not a valid Email address. Use following formats: example@address.com',]
            )
        ];

        $this->formBuilder->add(
            'Email',
            'text',
            [
                'label'       => 'Email',
                'label_attr'  => ['class' => 'control-label required-label'],
                'attr'        => ['class' => 'form-control', 'maxlength' => 50],
                'required'    => true,
                'data'        => $this->email,
                'constraints' => $constraints,
                'read_only'   => true
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addDateOfBirth()
    {
        $this->formBuilder->add(
            'DateOfBirth',
            'text',
            [
                'label'       => 'Date Of Birth',
                'label_attr'  => ['class' => 'control-label required-label'],
                'attr'        => ['class' => 'form-control date-birth', 'placeholder' => '25/06/1970'],
                'constraints' => [new Assert\NotBlank(['message' => 'Date Of Birth should not be empty.']),],
                'required'    => true,
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addSSN()
    {
        $constraints = [
            new Assert\NotBlank(['message' => 'Please enter your SSN']),
            new Assert\Regex(['pattern' => '/^[0-9]{9}+$/', 'match' => true, 'message' => 'SSN should have 9 digits']),
        ];

        $this->formBuilder->add(
            'SocialSecurityNumber',
            'number',
            [
                'label'       => 'Social Security Number (SSN)',
                'label_attr'  => ['class' => 'control-label required-label'],
                'attr'        => ['class' => 'form-control', 'maxlength' => 9, 'placeholder' => '123456789'],
                'constraints' => $constraints,
                'required'    => true,
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addEmploymentStatus()
    {

        $this->formBuilder->add(
            'EmploymentStatus',
            'choice',
            [
                'attr'       => ['class' => 'form-control'],
                'label'      => 'Employment Status',
                'label_attr' => ['class' => 'control-label required-label'],
                'required'   => true,
                'choices'    => [
                    'Employed'     => 'Employed',
                    'NotEmployed'  => 'Not Employed',
                    'SelfEmployed' => 'SelfEmployed',
                    'Student'      => 'Student'
                ],
                'multiple'   => false,
                'expanded'   => false,
            ]
        );

        return $this;
    }

    /**
     * @param string $name
     * @param bool   $isRequired
     *
     * @return $this
     */
    private function addAddress($name = 'One', $isRequired = true)
    {
        $constraints = [
            new Assert\Length(
                [
                    'min'        => 5,
                    'max'        => 100,
                    'minMessage' => $name . ' address should have minimum 5 and maximum 100 characters.',
                    'maxMessage' => $name . ' Primary address should have minimum 5 and maximum 100 characters'
                ]
            ),
            new Assert\Regex(
                [
                    'pattern' => '/^[a-zA-Z0-9\'# ]+$/',
                    'match'   => true,
                    'message' => ' Character Type Accepted Values: Alpha, numeric, spaces, apostrophes, ‘#’'
                ]
            ),
        ];

        $labelRequiredClass = '';
        if ($isRequired) {
            $constraints[] = new Assert\NotBlank(['message' => 'Please enter your ' . $name . ' address']);
            $labelRequiredClass = 'required-label';
        }

        $this->formBuilder->add(
            'StreetAddressLine' . $name,
            'text',
            [
                'label'       => 'Address ' . $name,
                'label_attr'  => ['class' => 'control-label ' . $labelRequiredClass],
                'attr'        => ['class' => 'form-control', 'maxlength' => 100],
                'constraints' => $constraints,
                'required'    => $isRequired
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addCity()
    {
        $constraints = [
            new Assert\NotBlank(['message' => 'Please enter your City']),
            new Assert\Regex(
                [
                    'pattern' => '/^[a-zA-Z ]+$/',
                    'match'   => true,
                    'message' => ' Character Type Accepted Values: Alpha, spaces'
                ]
            ),
            new Assert\Length(
                [
                    'min'        => 2,
                    'max'        => 50,
                    'minMessage' => ' City should have minimum 2 and maximum 50 characters.',
                    'maxMessage' => ' City should have minimum 2 and maximum 50 characters'
                ]
            ),
        ];

        $this->formBuilder->add(
            'City',
            'text',
            [
                'label'       => 'City',
                'label_attr'  => ['class' => 'control-label required-label'],
                'attr'        => ['class' => 'form-control', 'maxlength' => 50],
                'constraints' => $constraints,
                'required'    => true,
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addState()
    {
        $constraints = [new Assert\NotBlank(['message' => 'Please enter your State'])];

        $this->formBuilder->add(
            'State',
            'choice',
            [
                'choices'     => $this->locationService->getStates(),
                'attr'        => ['class' => 'form-control'],
                'label'       => 'State',
                'label_attr'  => ['class' => 'control-label required-label'],
                'constraints' => $constraints,
                'required'    => true
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addZip()
    {
        $constraints = [
            new Assert\NotBlank(['message' => 'Please enter your Zip']),
            new Assert\Regex(
                [
                    'pattern' => '/^\d{5}(?:[-]\d{4})?$/',
                    'match'   => true,
                    'message' => ' Character Type Accepted Values: Numeric, hyphen (e.g.80111-1234)'
                ]
            ),
            new Assert\Length(
                [
                    'min'        => 5,
                    'max'        => 10,
                    'minMessage' => ' Zip should have minimum 5 and maximum 10 characters.',
                    'maxMessage' => ' Zip should have minimum 5 and maximum 10 characters'
                ]
            ),
        ];
        $this->formBuilder->add(
            'Zip',
            'text',
            [
                'attr'        => [
                    'class'       => 'prop-details form-control',
                    'maxlength'   => 10,
                    'placeholder' => '80111-1234'
                ],
                'label'       => 'Zip',
                'label_attr'  => ['class' => 'control-label required-label'],
                'required'    => true,
                'constraints' => $constraints,
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addMobilePhone()
    {
        $constraints = [
            new Assert\NotBlank(['message' => 'Please enter your Mobile Phone Number']),
            new Assert\Regex(
                [
                    'pattern' => '/^\d{7,15}$/',
                    'match'   => true,
                    'message' => ' Only numeric characters number allowed (minimum 7 and maximum 15 characters)'
                ]
            ),
        ];

        $this->formBuilder->add(
            'MobilePhoneNumber',
            'text',
            [
                'label'       => 'Mobile Phone Number',
                'label_attr'  => ['class' => 'control-label required-label'],
                'attr'        => ['class' => 'form-control', 'placeholder' => '1234567890', 'maxlength' => 15],
                'required'    => true,
                'constraints' => $constraints,
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addHomePhone()
    {
        $constraints = [
            new Assert\NotBlank(['message' => 'Please enter your Home Phone Number']),
            new Assert\Regex(
                [
                    'pattern' => '/^[0-9]{7,15}+$/',
                    'match'   => true,
                    'message' => ' Only numeric characters number allowed (minimum 7 and maximum 15 characters)'
                ]
            ),
        ];

        $this->formBuilder->add(
            'HomePhoneNumber',
            'text',
            [
                'label'       => 'Home Phone Number',
                'label_attr'  => ['class' => 'control-label required-label'],
                'attr'        => ['class' => 'form-control', 'placeholder' => '1234567890', 'maxlength' => 15],
                'required'    => true,
                'constraints' => $constraints,
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addIncome()
    {
        $constraints = [
            new Assert\NotBlank(['message' => 'Please enter your Employment Income']),
            new Assert\Regex(
                [
                    'pattern' => '/^-?(?:\d+|\d*\.\d+)$/',
                    'match'   => true,
                    'message' => ' Only numeric characters number allowed'
                ]
            ),
            new Assert\Length(
                [
                    'min'        => 1,
                    'max'        => 12,
                    'minMessage' => ' Enter Employment Income in the range from $1 to $999999999999',
                    'maxMessage' => ' Enter Employment Income in the range from $1 to $999999999999',
                ]
            ),
            new Assert\Range(
                [
                    'min'        => 1,
                    'max'        => 999999999999,
                    'minMessage' => ' Employment Income should have minimum 1$ and maximum $999,999,999,999',
                    'maxMessage' => ' Employment Income should have minimum 1$ and maximum $999,999,999,999',
                ]
            )
        ];

        $this->formBuilder->add(
            'Income',
            'number',
            [
                'label'       => 'Income',
                'label_attr'  => ['class' => 'control-label required-label'],
                'attr'        => ['class' => 'form-control', 'placeholder' => '30000.00', 'maxlength' => 12],
                'required'    => true,
                'constraints' => $constraints,
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addIncomeFrequency()
    {
        $constraints = [new Assert\NotBlank(['message' => 'Please enter your Income Frequency'])];

        $this->formBuilder->add(
            'IncomeFrequency',
            'choice',
            [
                'choices'     => ['Monthly' => 'Monthly', 'Annual' => 'Annual'],
                'attr'        => ['class' => 'form-control'],
                'label'       => 'Income Frequency',
                'label_attr'  => ['class' => 'control-label required-label'],
                'constraints' => $constraints,
                'required'    => true
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addOtherIncome()
    {
        $constraints = [
            new Assert\Regex(
                [
                    'pattern' => '/^-?(?:\d+|\d*\.\d+)$/',
                    'match'   => true,
                    'message' => ' Only numeric characters number allowed'
                ]
            ),
            new Assert\Length(
                [
                    'min'        => 1,
                    'max'        => 12,
                    'minMessage' => ' Enter Other Income in the range from $0 to $999999999999',
                    'maxMessage' => ' Enter Other Income in the range from $0 to $999999999999',
                ]
            ),
            new Assert\Range(
                [
                    'min'        => 0,
                    'max'        => 999999999999,
                    'minMessage' => ' Other Income should have minimum $0 and maximum $999,999,999,999',
                    'maxMessage' => ' Other Income should have minimum $0 and maximum $999,999,999,999',
                ]
            )
        ];

        $this->formBuilder->add(
            'OtherIncome',
            'number',
            [
                'label'       => 'Other Income',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => ['class' => 'form-control', 'placeholder' => '30000.00', 'maxlength' => 12],
                'required'    => false,
                'constraints' => $constraints,
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addOtherIncomeFrequency()
    {
        $this->formBuilder->add(
            'OtherIncomeFrequency',
            'choice',
            [
                'choices'    => ['Monthly' => 'Monthly', 'Annual' => 'Annual'],
                'attr'       => ['class' => 'form-control'],
                'label'      => 'Other Income Frequency',
                'label_attr' => ['class' => 'control-label'],
                'required'   => false
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addAssetValue()
    {
        $constraints = [
            new Assert\Regex(
                [
                    'pattern' => '/^-?(?:\d+|\d*\.\d+)$/',
                    'match'   => true,
                    'message' => ' Only numeric characters number allowed'
                ]
            ),
            new Assert\Length(
                [
                    'min'        => 1,
                    'max'        => 12,
                    'minMessage' => ' Enter Other Income in the range from $0 to $999999999999',
                    'maxMessage' => ' Enter Other Income in the range from $0 to $999999999999',
                ]
            ),
            new Assert\Range(
                [
                    'min'        => 0,
                    'max'        => 999999999999,
                    'minMessage' => ' Other Income should have minimum $0 and maximum $999,999,999,999',
                    'maxMessage' => ' Other Income should have minimum $0 and maximum $999,999,999,999',
                ]
            )
        ];

        $this->formBuilder->add(
            'AssetValue',
            'number',
            [
                'label'       => 'Assets reported',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => ['class' => 'form-control', 'placeholder' => '30000.00', 'maxlength' => 12],
                'required'    => false,
                'constraints' => $constraints,
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addFCRA()
    {

        $label = 'I confirm that all entered data is complete, acurate and valid and accept the common FCRA agreement';
        $this->formBuilder->add(
            'FcraAgreementAccepted',
            'checkbox',
            [
                'required'   => false,
                'attr' => ['class' => 'fcra-checkbox'],
                'label'      => $label,
                'label_attr' => ['class' => 'control-label'],
            ]
        );

        return $this;
    }
}
