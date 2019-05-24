<?php

namespace Erp\VendorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class VendorEditType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('companyName', 'text', ['label' => 'Company Name', 'required' => false, 'attr' => ['class' => 'form-control', 'pattern' => '.{2,255}'],
                    'constraints' => [
                        new NotBlank(
                                [
                            'message' => 'Please enter Company Name',
                            'groups' => ['LandlordDetails']
                                ]
                        ),
                        new Length(
                                [
                            'min' => 2,
                            'max' => 255,
                            'minMessage' => 'Company Name cannot be less than 2 chars',
                            'maxMessage' => 'Company Name cannot be longer than 255 chars',
                            'groups' => ['LandlordDetails']
                                ]
                        )
                    ]
                ])
                ->add('businessType', 'text', ['label' => 'Business Type', 'required' => false, 'attr' => ['class' => 'form-control', 'pattern' => '.{2,255}'],
                    'constraints' => [
                        new NotBlank(
                                [
                            'message' => 'Please enter Business Type',
                            'groups' => ['LandlordDetails']
                                ]
                        ),
                        new Length(
                                [
                            'min' => 2,
                            'max' => 255,
                            'minMessage' => 'Business Type cannot be less than 2 chars',
                            'maxMessage' => 'Business Type cannot be longer than 255 chars',
                            'groups' => ['LandlordDetails']
                                ]
                        )
                    ]
                ])
                ->add('website', 'text', ['label' => 'Company Website', 'required' => false, 'attr' => ['class' => 'form-control', 'pattern' => '.{2,255}'],
                    'constraints' => [
                        new NotBlank(
                                [
                            'message' => 'Please enter Company Website',
                            'groups' => ['LandlordDetails']
                                ]
                        ),
                        new Length(
                                [
                            'min' => 2,
                            'max' => 255,
                            'minMessage' => 'Company Website cannot be less than 2 chars',
                            'maxMessage' => 'Company Website cannot be longer than 255 chars',
                            'groups' => ['LandlordDetails']
                                ]
                        )
                    ]
                ])
                ->add('address', 'text', ['label' => 'Address', 'required' => false, 'attr' => ['class' => 'form-control', 'pattern' => '.{2,255}'],
                    'constraints' => [
                        new NotBlank(
                                [
                            'message' => 'Please enter Address',
                            'groups' => ['LandlordDetails']
                                ]
                        ),
                        new Length(
                                [
                            'min' => 2,
                            'max' => 255,
                            'minMessage' => 'Address cannot be less than 2 chars',
                            'maxMessage' => 'Address cannot be longer than 255 chars',
                            'groups' => ['LandlordDetails']
                                ]
                        )
                    ]
                ])
                ->add('firstName', 'text', ['label' => 'First Name', 'required' => false, 'attr' => ['class' => 'form-control', 'pattern' => '.{2,255}'],
                    'constraints' => [
                        new NotBlank(
                                [
                            'message' => 'Please enter First Name',
                            'groups' => ['LandlordDetails']
                                ]
                        ),
                        new Length(
                                [
                            'min' => 2,
                            'max' => 255,
                            'minMessage' => 'First Name cannot be less than 2 chars',
                            'maxMessage' => 'First Name cannot be longer than 255 chars',
                            'groups' => ['LandlordDetails']
                                ]
                        )
                    ]
                ])
                ->add('lastName', 'text', ['label' => 'Last Name', 'required' => false, 'attr' => ['class' => 'form-control', 'pattern' => '.{2,255}'],
                    'constraints' => [
                        new NotBlank(
                                [
                            'message' => 'Please enter Last Name',
                            'groups' => ['LandlordDetails']
                                ]
                        ),
                        new Length(
                                [
                            'min' => 2,
                            'max' => 255,
                            'minMessage' => 'Last Name cannot be less than 2 chars',
                            'maxMessage' => 'Last Name cannot be longer than 255 chars',
                            'groups' => ['LandlordDetails']
                                ]
                        )
                    ]
                ])
                ->add('contactEmail', 'email', ['label' => 'Contact Email', 'required' => false, 'attr' => ['class' => 'form-control'],
                    'constraints' => [
                        new NotBlank(
                                [
                            'message' => 'Contact Email cannot be empty.',
                            'groups' => ['LandlordDetails']
                                ]
                        ),
                        new Email(
                                [
                            'message' => 'Lanlord must have valid email.',
                            'groups' => ['LandlordDetails']
                                ]
                        )
                    ]
                ])
                ->add('contactPhone', 'text', ['label' => 'Contact Phone', 'required' => false, 'attr' => ['class' => 'form-control', 'pattern' => '.{2,255}'],
                    'constraints' => [
                        new NotBlank(
                                [
                            'message' => 'Please enter Contact Phone',
                            'groups' => ['LandlordDetails']
                                ]
                        ),
                        new Length(
                                [
                            'min' => 2,
                            'max' => 255,
                            'minMessage' => 'Contact Phone cannot be less than 2 chars',
                            'maxMessage' => 'Contact Phone cannot be longer than 255 chars',
                            'groups' => ['LandlordDetails']
                                ]
                        )
                    ]
                ])
                ->add('submit', 'submit', [
                    'label' => 'Save',
                    'attr' => [
                        'class' => 'btn edit-btn btn-space'
                    ]
                ])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Erp\VendorBundle\Entity\VendorEdit'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'erp_vendorbundle_vendoredit';
    }

}
