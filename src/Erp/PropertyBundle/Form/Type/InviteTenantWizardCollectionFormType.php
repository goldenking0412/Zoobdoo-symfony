<?php
namespace Erp\PropertyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Erp\PropertyBundle\Form\Type\InviteTenantWizardFormType;
/**
 * Class InviteTenantFormType
 *
 * @package Erp\PropertyBundle\Form\Type
 */
class InviteTenantWizardCollectionFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      
          $builder->add('invitedUsers', 'collection', array(
            'type' => new InviteTenantWizardFormType(),
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'options' => array('label' => false),
          ));
    }

    /**
     * Form default options
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'        => 'Erp\PropertyBundle\Entity\Property',
                'validation_groups' => ['InvitedUser']
            ]
        );
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
