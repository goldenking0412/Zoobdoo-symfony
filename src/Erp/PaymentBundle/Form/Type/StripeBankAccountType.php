<?php

namespace Erp\PaymentBundle\Form\Type;

use Erp\PaymentBundle\Stripe\Model\BankAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\FormInterface;

class StripeBankAccountType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'accountHolderName',
                'text',
                [
                    'attr'        => ['class' => 'form-control'],
                    'label'       => 'Account holder name',
                    'label_attr'  => ['class' => 'control-label required-label'],
                    'required'    => true,
                    'max_length'  => 100,
                    'constraints' => [
                        new NotBlank(['message' => 'Please enter your country']),
                    ],
                ]
            )
            ->add(
                'accountHolderType',
                'choice',
                [
                    'attr'       => ['class' => 'form-control select-control'],
                    'label'      => 'Select account holder type',
                    'label_attr' => ['class' => 'control-label required-label'],
                    'required'   => true,
                    'choices'    => [
                        'individual' => 'Individual',
                        'company' => 'Company',
                    ],
                    'multiple'   => false,
                    'expanded'   => false,
                ]
            )
            ->add(
                'accountNumber',
                'text',
                [
                    'attr'        => ['class' => 'form-control'],
                    'label'       => 'Account Number',
                    'label_attr'  => ['class' => 'control-label required-label'],
                    'required'    => true,
                    'max_length'  => 100,
                    'constraints' => [
                        new NotBlank(['message' => 'Please enter your Bank Account Number']),
                        new Length(
                            [
                                'min'        => 4,
                                'max'        => 100,
                                'minMessage' => 'Bank Account Number should have minimum 4 characters and maximum 100 digits',
                                'maxMessage' => 'Bank Account Number should have minimum 4 characters and maximum 100 digits'
                            ]
                        ),
                        new Regex(
                            [
                                'pattern' => '/^[0-9]+$/',
                                'match'   => true,
                                'message' => 'Bank Account Number must contains only digits'
                            ]
                        )
                    ],
                ]
            )
            ->add(
                'country',
                'text',
                [
                    'attr'        => ['class' => 'form-control'],
                    'label'       => 'Country',
                    'label_attr'  => ['class' => 'control-label required-label'],
                    'required'    => true,
                    'max_length'  => 100,
                    'constraints' => [
                        new NotBlank(['message' => 'Please enter your country']),
                    ],
                ]
            )
            ->add(
                'currency',
                'text',
                [
                    'attr'        => ['class' => 'form-control'],
                    'label'       => 'Currency',
                    'label_attr'  => ['class' => 'control-label required-label'],
                    'required'    => true,
                    'max_length'  => 100,
                    'constraints' => [
                        new NotBlank(['message' => 'Please enter your currency']),
                    ],
                ]
            )
            ->add(
                'routingNumber',
                'text',
                [
                    'attr'        => ['class' => 'form-control'],
                    'label'       => 'Routing Number',
                    'label_attr'  => ['class' => 'control-label'],
                    'required'    => false,
                    'max_length'  => 100,
                    'constraints' => [
                        new NotBlank(['message' => 'Please enter your routing number', 'groups' => ['routingNumber']]),
                    ],
                    'validation_groups' => function (FormInterface $form) {
                        /** @var BankAccount $data */
                        $data = $form->getParent()->getData();

                        if (!isset($data) || 'US' == $data->getCountry() || !$form->isSubmitted()) {
                            return ['routingNumber'];
                        }

                        return;
                    },
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
        ;
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BankAccount::class,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'erp_stripe_bank_account';
    }
}