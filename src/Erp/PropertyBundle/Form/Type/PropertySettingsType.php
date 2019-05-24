<?php

namespace Erp\PropertyBundle\Form\Type;

use Erp\PropertyBundle\Entity\PropertySettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropertySettingsType extends AbstractType {

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $months = array_combine(range(1, 31), range(1, 31));
        $builder
                ->add('dayUntilDue', 'choice', [
                    'label' => 'Rent Due',
                    'attr' => ['class' => 'form-control select-control'],
                    'choices' => $months,
                    'choices_as_values' => true,
                ])
                ->add('moveInDate', 'date', [
                    'label' => 'Move in',
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'attr' => ['class' => 'form-control'],
                    'required' => false,
                ])
                ->add('leaseEnd', 'date', [
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'attr' => ['class' => 'form-control'],
                    'required' => false,
                ])
                ->add('paymentAmount', 'money', [
                    'currency' => false,
                    'label' => 'Rent Amount',
                    'attr' => ['class' => 'form-control'],
                ])
                ->add('allowPartialPayments', 'checkbox', [
                    'label' => 'Restrict Partial Payments',
                    'required' => false,
                        ]
                )
                ->add('atWill', 'checkbox', [
                    'label' => 'At Will',
                    'required' => false,
                ])
                ->add('termLease', 'checkbox', [
                    'label' => 'Term Lease',
                    'required' => false,
                ])
                ->add('allowCreditCardPayments', 'checkbox', [
                    'label' => 'Allow Credit Card Payments',
                    'required' => false,
                ])
                ->add('allowAutoDraft', 'checkbox', [
                    'label' => 'Set auto-draft from tenant account?',
                    'required' => false,
                ])
        ;
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => PropertySettings::class,
            'csrf_protection' => false,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName() {
        return 'erp_property_payment_settings';
    }

}
