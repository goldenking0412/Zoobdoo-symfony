<?php

namespace Erp\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as BaseAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class EmailNotifications
 *
 * @package Erp\AdminBundle\Admin
 */
class EmailNotifications extends BaseAdmin
{
    protected $baseRoutePattern = 'settings/emailnotifications';

    protected $baseRouteName = 'admin_erpuserbundle_emailnotifications';

    protected $formOptions = [
        'validation_groups' => ['EmailNotification']
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
        $formMapper
            ->add('subject', 'text')
            ->add('title', 'text', ['label' => 'Body Title'])
            ->add('body', 'textarea', ['label' => 'Body Text', 'required' => false])
            ->add('button', 'text', ['label' => 'Button Text'])
        ;
    }

    /**
     * Fields to be shown on lists
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name', 'text')
            ->add('subject', 'text')
            ->add('_action', 'actions', ['actions' => ['edit' => []]]);
    }
}
