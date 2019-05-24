<?php

namespace Erp\PropertyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Erp\PropertyBundle\Entity\ApplicationCreditCard;
use Erp\StripeBundle\Form\Type\CreditCardType;
use Symfony\Component\Validator\Constraints as Assert;

class ApplicationCreditCardType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'creditCard',
                new CreditCardType()
            )
            ->add(
                'email',
                'text',
                [
                    'label' => 'Email',
                    'label_attr' => ['class' => 'control-label required-label'],
                    'attr' => [
                        'class' => 'form-control',
                    ],
                    'constraints' => [
                        new Assert\Email(),
                    ],
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
            'csrf_protection' => false,
            'data_class' => ApplicationCreditCard::class,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'erp_property_application_credit_card_type';
    }
}