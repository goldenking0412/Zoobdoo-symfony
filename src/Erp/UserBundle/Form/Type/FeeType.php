<?php

namespace Erp\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Erp\UserBundle\Entity\Fee;

class FeeType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'amount',
                'money',
                [
                    'required' => false,
                    'label' => 'Fee',
                    'currency' => false,
                ]
            )
            ->add(
                'type',
                'choice',
                [
                    'required' => false,
                    'label' => 'Category',
                    'choices' => [
                        Fee::FEE_PAYMENT_TYPE => 'Late Fees',
                    ]
                ]
            );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Fee::class,
            'csrf_protection' => false,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'erp_user_fee';
    }
}