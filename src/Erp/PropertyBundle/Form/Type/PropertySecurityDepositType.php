<?php
// src/AppBundle/Form/TaskType.php
namespace Erp\PropertyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class PropertySecurityDepositType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
              'notWantSecurityDeposit',
              CheckboxType::class,
              [
                  'label' => 'I do not want to add a security Deposit',
                  'required' => false,
              ]
            )
            ->add(
              'yesWantSecurityDeposit',
              CheckboxType::class,
              [
                  'label' => 'Yes, I want to add a security deposit',
                  'required' => false,
              ]
            )
            ->add(
                'amount',
                'money',
                [
                    'currency' => false,
                    'label' => 'Deposit Amount',
                    'label_attr' => ['class' => 'control-label'],
                    'attr' => ['class' => 'form-control col-xs-4'],
                ]
            )
            ->add(
                'sendToMainAccount',
                'checkbox',
                [
                    'label' => 'Send Security Deposit to main account',
                    'required' => false,
                ]
            )
            ->add(
                'addBankAccount',
                'checkbox',
                [
                    'label' => 'Add separate bank account for security deposits',
                    'required' => false,
                ]
            )
            ->add('save', 'submit')
        ;
    }

    public function getName()
    {
        return 'erp_property_security_deposit';
    }
}
