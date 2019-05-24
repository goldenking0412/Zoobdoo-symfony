<?php

namespace Erp\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as BaseAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class Fees
 *
 * @package Erp\AdminBundle\Admin
 */
class Fees extends BaseAdmin
{
    protected $baseRoutePattern = 'settings/fees';

    protected $baseRouteName = 'admin_erpuserbundle_fees';

    protected $formOptions = [
        'validation_groups' => ['AdminCreated', 'updateOptions']
    ];

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
        $collection->clearExcept(['list', 'show', 'edit']);
    }

    /**
     * Fields to be shown on create/edit forms
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $createApplicationFormFeeParams = ['currency' => 'USD', 'label' => 'Online Rental Application Fee'];
        $applicationFormAnonymousFeeParams =
            ['currency' => 'USD', 'label' => 'Online Rental Application Fee (Applicants)'];

        $formMapper
            ->add('erentpay', 'money', ['currency' => 'USD', 'label' => 'Monthly Fee'])
            ->add('rentAllowance', 'money', ['currency' => 'USD', 'label' => 'Bank Transaction Fee'])
            ->add('ccTransactionFee', 'number', ['label' => 'CCard Transaction Fee (%)'])
            ->add('backgroundCheck', 'money', ['currency' => 'USD', 'label' => 'Tenant Screening Fee'])
            ->add('askProCheck', 'money', ['currency' => 'USD', 'label' => 'Ask a Pro Fee'])
            ->add('propertyFee', 'money', ['currency' => 'USD', 'label' => 'Add Property Fee'])
            ->add('postVacancyOnlineFee', 'money', ['currency' => 'USD', 'label' => 'Post Vacancy Online Fee'])
            ->add('createApplicationFormFee', 'money', $createApplicationFormFeeParams)
            ->add('applicationFormAnonymousFee', 'money', $applicationFormAnonymousFeeParams)
            ->add('createContractFormFee', 'money', ['currency' => 'USD', 'label' => 'Online Rental Contract Fee'])
            ->add('eSignFee', 'money', ['currency' => 'USD', 'label' => 'eSign Fee'])
            ->add('checkPaymentFee', 'money', ['currency' => 'USD', 'label' => 'Card Verification Payment'])
            ->add('defaultEmail', 'email', ['label' => 'System Email for notification'])
            ->add('smartMoveEnable', 'checkbox', ['label' => 'Tenant Screening enabled', 'required' => false])
        ;
    }

    /**
     * Fields to be shown on lists
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $createApplicationFormFeeParams = ['currency' => 'USD', 'label' => 'Online Rental Application Fee'];

        $listMapper
            ->add('erentpay', 'money', ['currency' => 'USD', 'label' => 'Monthly Fee'])
            ->add('rentAllowance', 'money', ['currency' => 'USD', 'label' => 'Transaction Fee'])
            ->add('backgroundCheck', 'money', ['currency' => 'USD', 'label' => 'Tenant Screening Fee'])
            ->add('askProCheck', 'money', ['currency' => 'USD', 'label' => 'Ask a Pro Fee'])
            ->add('propertyFee', 'money', ['currency' => 'USD', 'label' => 'Add Property Fee'])
            ->add('postVacancyOnlineFee', 'money', ['currency' => 'USD', 'label' => 'Post Vacancy Online Fee'])
            ->add('createApplicationFormFee', 'money', $createApplicationFormFeeParams)
            ->add('createContractFormFee', 'money', ['currency' => 'USD', 'label' => 'Online Rental Contract Fee'])
            ->add('defaultEmail', 'email', ['label' => 'System Email for notification'])
            ->add('eSignFee', 'money', ['currency' => 'USD', 'label' => 'eSign Fee'])
            ->add('_action', 'actions', ['actions' => ['edit' => []]]);
    }
}
