<?php

namespace Erp\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as BaseAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Doctrine\ORM\Query\Expr;

/**
 * Class PropertyRepostRequests
 *
 * @package Erp\AdminBundle\Admin
 */
class Eviction extends BaseAdmin {

    /**
     * @var string
     */
    protected $baseRoutePattern = 'settings/eviction';

    /**
     * @var string
     */
    protected $baseRouteName = 'admin_erppropertybundle_evictionrequests';

    /**
     * @var array
     */
    protected $formOptions = [
        'validation_groups' => ['EvictionData']
    ];

    /**
     * @var array
     */
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_by' => 'createdAt',
        '_sort_order' => 'DESC',
    );

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection) {
        $collection->clearExcept(['list', 'edit']);
        $collection->add('listpdf');
    }

    /**
     * Fields to be shown on create/edit forms
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper) {
        $formMapper->add('description');
    }

    /**
     * Fields to be shown on lists
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper) {
        $listMapper
                ->add('id', 'integer')
                ->add('properties.user.firstName', 'string', ['label' => 'Manager First Name'])
                ->add('properties.user.lastName', 'string', ['label' => 'Manager Last Name'])
                ->add('properties.name', 'string', ['label' => 'Property Name'])
                ->add('properties.address', 'string', ['label' => 'Property Address'])
                ->add('properties.city.name', 'string', ['label' => 'Property City'])
                ->add('properties.city.zip', 'string', ['label' => 'Property Zip Code'])
                ->add('properties.tenantUser.firstName', 'string', ['label' => 'Tenant First Name'])
                ->add('properties.tenantUser.lastName', 'string', ['label' => 'Tenant Last Name'])
                ->add('description', 'string', ['label' => 'Other Details'])
                ->add('days', 'string', ['label' => 'Days After Due Date'])
                ->add('template.title', 'string', ['label' => 'Select Template For Eviction', 'template' => 'ErpAdminBundle:Options:list_field_pdf.html.twig'])
                ->add('createdAt', 'datetime', ['label' => 'Created At'])
                ->add('_action', 'actions', ['actions' => ['edit' => '']]);
    }

    /**
     * Fields to be shown on filter forms
     *
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper
                ->add('id', null, array('label' => 'Tracking number'))
                ->add('properties.address')
                ->add('properties.city.name')
                ->add('properties.city.zip')
        ;
    }

}
