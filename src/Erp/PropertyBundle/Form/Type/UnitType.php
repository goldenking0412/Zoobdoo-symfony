<?php

namespace Erp\PropertyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Erp\PropertyBundle\Entity\Unit;

class UnitType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'quantity',
                'text',
                [
                    /*'label' => 'Number of units:',
                    'label_attr' => ['class' => 'control-label required-label'],*/
                    'attr' => ['class' => 'form-control'],
                ]
            )
            ->add(
                'submit',
                'submit',
                [
                    'label' => 'Submit',
                    'attr' => [
                        'class' => 'btn-circle'
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
            'data_class' => Unit::class,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'erp_payment_unit';
    }
}