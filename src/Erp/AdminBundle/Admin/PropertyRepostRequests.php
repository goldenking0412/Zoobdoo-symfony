<?php

namespace Erp\AdminBundle\Admin;

use Erp\PropertyBundle\Entity\PropertyRepostRequest;
use Sonata\AdminBundle\Admin\AbstractAdmin as BaseAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class PropertyRepostRequests
 *
 * @package Erp\AdminBundle\Admin
 */
class PropertyRepostRequests extends BaseAdmin
{
    /**
     * @var string
     */
    protected $baseRoutePattern = 'properties-management/repost-requests';

    /**
     * @var string
     */
    protected $baseRouteName = 'admin_erppropertybundle_repostrequests';

    /**
     * @var array
     */
    protected $formOptions = [
        'validation_groups' => ['PropertyRepostRequest']
    ];

    /**
     * @var array
     */
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_by' => 'status',
    );

    /**
     * @var array
     */
    protected static $statuses = [
        PropertyRepostRequest::STATUS_NEW      => 'New (Paid)',
        PropertyRepostRequest::STATUS_ACCEPTED => 'Posted',
        PropertyRepostRequest::STATUS_REJECTED => 'Rejected'
    ];

    /**
     * Fields to be shown on create/edit forms
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add(
            'status',
            'choice',
            [
                'choices' => self::$statuses
            ]
        )
        ->add(
            'note',
            'textarea',
            [
                'attr' => ['maxlength' => '255'],
                'required' => false,
                'label' => 'Note (It will be shown on listings page for Manager)'
            ]
        );
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list', 'delete', 'edit', 'batch','export']);
        $collection->add('csv');
        $collection->add('xml');
    }

    /**
     * Fields to be shown on lists
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('property.name', 'string')
            ->add('property.user.firstName', 'string', ['label' => 'First Name'])
            ->add('property.user.lastName', 'string', ['label' => 'Last Name'])
            ->add('property.user.email', 'string', ['label' => 'Email'])
            ->add('property.user.phone', 'string', ['label' => 'Phone'])
            ->add('status', 'text', ['template' => 'ErpAdminBundle:PropertyRepostRequests:list_field_status.html.twig'])
            ->add('_action', 'actions', ['actions' => ['edit' => [], 'delete' => []]]);
    }

    /**
     * Fields to be shown on filter forms
     *
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('status', 'doctrine_orm_string', [], 'choice', ['choices'=> self::$statuses]);
    }
}
