<?php

namespace Erp\StripeBundle\Form\Type;

use Erp\StripeBundle\Entity\CreditCard;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CreditCardType extends AbstractType {

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('firstName', 'text', [
                    'required' => false,
                    'label' => 'First Name',
                    'label_attr' => ['class' => 'control-label'],
                    'attr' => [
                        'class' => 'form-control',
                    ],
                ])
                ->add('middleName', 'text', [
                    'required' => false,
                    'label' => 'Middle Name',
                    'label_attr' => ['class' => 'control-label'],
                    'attr' => [
                        'class' => 'form-control',
                    ],
                ])
                ->add('lastName', 'text', [
                    'required' => false,
                    'label' => 'Last Name',
                    'label_attr' => ['class' => 'control-label'],
                    'attr' => [
                        'class' => 'form-control',
                    ],
                ])
                ->add('number', 'text', [
                    'required' => true,
                    'label' => 'Card Number',
                    'label_attr' => ['class' => 'control-label required-label'],
                    'attr' => [
                        'class' => 'form-control',
                        'data-stripe' => 'number',
                    ],
                    'constraints' => [
                        new Assert\NotBlank(['message' => 'Please enter credit card number']),
                        new Assert\Length(
                                [
                            'min' => 12,
                            'max' => 19,
                            'minMessage' => 'Cannot be less than 12 symbols',
                            'maxMessage' => 'Cannot be greater than 19 symbols',
                                ]
                        ),
                        new Assert\Luhn(['message' => 'Invalid card number']),
                    ],
                ])
                ->add('cvc', 'integer', [
                    'required' => true,
                    'label' => 'CVC',
                    'label_attr' => ['class' => 'control-label required-label'],
                    'attr' => [
                        'class' => 'form-control',
                        'data-stripe' => 'cvc',
                    ],
                    'constraints' => [
                        new Assert\NotBlank(['message' => 'Please enter a security code']),
                        new Assert\Length(
                                [
                            'min' => 3,
                            'max' => 4,
                            'minMessage' => 'Cannot be less than 3 symbols',
                            'maxMessage' => 'Cannot be greater than 4 symbols',
                                ]
                        ),
                    ]
                ])
                ->add('expMonth', 'choice', [
                    'required' => true,
                    'choices' => array_combine(range(1, 12), range(1, 12)),
                    'label' => 'Month',
                    'label_attr' => ['class' => 'control-label required-label'],
                    'attr' => [
                        'class' => 'form-control',
                        'data-stripe' => 'exp-month',
                    ],
                    'constraints' => [
                        new Assert\NotBlank(['message' => 'Please enter an expiry month']),
                        new Assert\Range(
                                [
                            'min' => 1,
                            'max' => 12,
                            'minMessage' => 'Cannot be less than 1',
                            'maxMessage' => 'Cannot be greater than 12'
                                ]
                        ),
                    ],
                ])
                ->add('expYear', 'choice', [
                    'required' => true,
                    'choices' => array_combine(range(date('Y'), date('Y') + 10), range(date('Y'), date('Y') + 10)),
                    'label' => 'Year',
                    'label_attr' => ['class' => 'control-label required-label'],
                    'attr' => [
                        'class' => 'form-control',
                        'data-stripe' => 'exp-year',
                    ],
                    'constraints' => [
                        new Assert\NotBlank(['message' => 'Please enter an expiry year']),
                        new Assert\Range(
                                [
                            'min' => date('Y'),
                            'max' => date('Y', strtotime('+20 years')),
                            'minMessage' => 'Expiry year cannot be less than current',
                            'maxMessage' => 'Expiry year cannot be greater than 20 years'
                                ]
                        ),
                    ],
                ])
                ->add('submit', 'submit', [
                    'label' => 'Submit',
                    'attr' => [
                        'class' => 'btn-circle',
                    ]
                ])
                ->add('token', 'hidden')
                ->add('js_error_message', 'hidden', ['mapped' => false]); //transporting from Stripe JS API to Symfony errors
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(
                [
                    'data_class' => CreditCard::class,
                ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getName() {
        return 'erp_stripe_credit_card';
    }

}
