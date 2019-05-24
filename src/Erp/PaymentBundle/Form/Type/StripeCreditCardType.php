<?php

namespace Erp\PaymentBundle\Form\Type;

use Erp\PaymentBundle\Stripe\Model\CreditCard;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class StripeCreditCardType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('number',
                'text',
                [
                    'required' => true,
                    'label' => 'Card Number',
                    'label_attr' => ['class' => 'control-label required-label'],
                    'attr' => [
                        'class' => 'form-control',
                        'data-stripe' => 'number',
                    ],
                    'constraints' => [
                        new Assert\NotBlank(['message' => 'Please enter a card number']),
                        new Assert\Length(
                            [
                                'min' => 12,
                                'max' => 19,
                                'minMessage' => 'Invalid card number',
                                'maxMessage' => 'Invalid card number'
                            ]
                        ),
                        new Assert\Luhn(['message' => 'Invalid card number']),
                    ],
                ]
            )
            ->add('cvc',
                'text',
                [
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
                                'minMessage' => 'Invalid security code',
                                'maxMessage' => 'Invalid security code',
                            ]
                        ),
                    ]
                ]
            )
            ->add(
                'expMonth',
                'choice',
                [
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
                                'minMessage' => 'Invalid expiry month',
                                'maxMessage' => 'Invalid expiry month'
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'expYear',
                'choice',
                [
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
                                'minMessage' => 'Invalid expiry year',
                                'maxMessage' => 'Invalid expiry year'
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'submit',
                'submit',
                [
                    'label' => 'Submit',
                    'attr' => [
                        'class' => 'btn submit-popup-btn'
                    ]
                ]
            )
            ->add('token', 'hidden');
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => CreditCard::class,
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'erp_stripe_credit_card';
    }
}