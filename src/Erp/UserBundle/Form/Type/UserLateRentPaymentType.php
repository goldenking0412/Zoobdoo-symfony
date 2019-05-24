<?php

namespace Erp\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Erp\UserBundle\Entity\User;

class UserLateRentPaymentType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'allowRentPayment',
                'checkbox',
                [
                    'label' => 'Allow Rent Payment',
                ]
            )
            ->add(
                'allowPartialPayment',
                'checkbox',
                [
                    'required' => false,
                    'label' => 'No Partial Payments Accepted',
                ]
            );;
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => false,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'erp_user_user_late_rent_payment';
    }
}