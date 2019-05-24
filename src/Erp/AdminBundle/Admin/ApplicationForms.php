<?php

namespace Erp\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as BaseAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class ApplicationForm
 *
 * @package Erp\AdminBundle\Admin
 */
class ApplicationForms extends BaseAdmin
{
    /**
     * @var string
     */
    protected $baseRoutePattern = 'properties-management/application-forms';

    /**
     * @var string
     */
    protected $baseRouteName = 'admin_erpuserbundle_application_forms';

    /**
     * @var array
     */
    protected $formOptions = [
        'validation_groups' => ['UserDocuments']
    ];

    /**
     * Default Datagrid values
     *
     * @var array
     */
    protected $datagridValues = [
        '_page' => 1,
        '_sort_order' => 'DESC',
    ];

    /**
     * @param string $context
     *
     * @return mixed
     */
    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        $query->andWhere($query->getRootAliases()[0] . '.fromUser IS NULL');

        return $query;
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('batch');
        $collection->remove('edit');
        $collection->clearExcept(['list']);
    }

    /**
     * Fields to be shown on lists
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add(
                'toUser.lastName',
                null,
                [
                    'label' => 'Manager',
                    'template' => 'ErpAdminBundle:ApplicationForm:list_field_to_user.html.twig',
                ]
            )
            ->add('toUser.email', null, ['label' => 'Manager\'s Email'])
            ->add(
                'document.originalName',
                null,
                [
                    'label' => 'Application Form',
                    'template' => 'ErpAdminBundle:ApplicationForm:list_field_document.html.twig'
                ]
            )
            ->add('createdDate')
        ;
    }

    /**
     * Fields to be shown on filter forms
     *
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('toUser.lastName', null, ['label' => 'Manager\'s Last Name'])
            ->add('toUser.email', null, ['label' => 'Manager\'s Email'])
            ->add('createdDate', 'doctrine_orm_date_range')
        ;
    }
}
