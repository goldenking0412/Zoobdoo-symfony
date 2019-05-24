<?php
namespace Erp\PropertyBundle\Form\Type;

use Erp\UserBundle\Entity\InvitedUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class InviteTenantFormType
 *
 * @package Erp\PropertyBundle\Form\Type
 */
class InviteTenantFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'invitedEmail',
            'email',
            [
                'attr'       => ['class' => 'form-control contact-email', 'placeholder' => 'Email'],
                'label'      => false,
                'label_attr' => ['class' => 'control-label required-label'],
                'required'   => true,
            ]
        );

        $builder->add(
            'submit',
            'submit',
            ['label' => 'Add Tenant', 'attr' => ['class' => 'btn edit-btn']]
        );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
                'data_class'        => InvitedUser::class,
                'validation_groups' => ['InvitedUser']
        ]);
    }

    /**
     * Form name
     *
     * @return string
     */
    public function getName()
    {
        return 'erp_invite_user_form';
    }
}
