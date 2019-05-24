<?php

namespace Erp\PropertyBundle\Form\Type;

use Erp\PropertyBundle\Entity\PropertySettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropertyRefundDepositType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'amount',
                'number',
                [
                    'label'      => 'Amount',
                    'attr'       => ['class' => 'prop-details prop-price form-control', 'placeholder' => '199'],
                    'label_attr' => ['class' => 'control-label required-label'],
                    'required' => true,
                    'constraints' => [
                        new Assert\NotBlank(['message' => 'Amount should not be empty']),
                        new Assert\Length(
                            [
                                'min' => 1,
                                'max' => 4,
                                'minMessage' => 'Enter the amount in the range from $1 to $9999',
                                'maxMessage' => 'Enter the amount in the range from $1 to $9999',
                            ]
                        ),
                        new Assert\Range(
                            [
                                'min' => 0.01,
                                'max' => $options['amount'],
                                'minMessage' => 'Amount should have minimum 0.01$ and maximum $1,000,000',
                                'maxMessage' => 'Amount should have minimum 0.01$ and maximum $1,000,000',
                            ]
                        )
                    ],
                ]
            )
            ->add(
                'startPaymentAt',
                'date',
                [
                    'label' => 'Refund Date',
                    'attr' => [
                        'class' => 'prop-details form-control subject date',
                        'placeholder' => 'Refund Date',
                    ],
                    'label_attr' => ['class' => 'control-label required-label'],
                    'required' => true,
                    'constraints' => [
                        new Assert\NotBlank(['message' => 'Payment Date should not be empty']),
                        new Assert\Date(['message' => 'Is not a valid date format']),
                        new Assert\GreaterThanOrEqual(['value' => 'today', 'message' => 'Please select today\'s or future date.']),
                    ],
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                ]
            )            
            ->add(
                'refund',
                'choice',
                [
                    'label' => 'Refund',
                    'label_attr' => ['class' => 'control-label required-label'],
                    'attr' => ['class' => 'prop-details form-control'],
                    'choices' => [1 => 'Yes', 0 => 'No'],
                    'required' => true,
                ]
            )
            ->add(
                'submit',
                'submit',
                ['label' => 'Submit', 'attr' => ['class' => 'btn-circle']]
            );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'csrf_protection'   => false,
            'amount' => 10000
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'erp_property_refund_deposit';
    }
}