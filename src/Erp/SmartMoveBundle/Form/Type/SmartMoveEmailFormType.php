<?php

namespace Erp\SmartMoveBundle\Form\Type;

use Erp\SmartMoveBundle\Entity\SmartMoveRenter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SmartMoveEmailFormType
 *
 * @package Erp\PaymentBundle\Form\Type
 */
class SmartMoveEmailFormType extends AbstractType
{
    /**
     * Build form function
     *
     * @param FormBuilderInterface $builder the formBuilder
     * @param array                $options the options for this form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'email',
            'email',
            [
                'label'       => false,
                'attr'        => ['class' => 'form-control', 'placeholder' => 'Tenant Email'],
                'required'    => true,
                'constraints' => [
                    new Assert\NotBlank(['message' => ' Email should have minimum 6 and maximum 255 characters']),
                    new Assert\Length(
                        [
                            'min'        => 6,
                            'max'        => 255,
                            'minMessage' => ' Email should have minimum 6 and maximum 255 characters',
                            'maxMessage' => ' Email should have minimum 6 and maximum 255 characters'
                        ]
                    ),
                    new Assert\Email(
                        [
                            'message' => ' This value is not a valid Email address.
                        Use following formats: example@address.com',
                        ]
                    )
                ],
            ]
        )->add(
            'go',
            'submit',
            ['label' => 'GO', 'attr' => ['class' => 'btn-circle lightbox-ajax']]
        );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SmartMoveRenter::class,
            'csrf_protection'   => false,
        ]);
    }

    /**
     * Return unique name for this form
     *
     * @return string
     */
    public function getName()
    {
        return 'sm_email_form';
    }
}
