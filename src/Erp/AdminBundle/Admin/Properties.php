<?php

namespace Erp\AdminBundle\Admin;

use Erp\PropertyBundle\Entity\Property;
use Sonata\AdminBundle\Admin\AbstractAdmin as BaseAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Doctrine\ORM\EntityRepository;
use Erp\CoreBundle\Form\ImageType;
use Erp\CoreBundle\Form\DocumentType;
use Erp\UserBundle\Entity\User;
use Sonata\AdminBundle\Route\RouteCollection;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;
use Doctrine\ORM\Query\Expr;

/**
 * Class Properties
 * @package Erp\AdminBundle\Admin
 */
class Properties extends BaseAdmin {

    /**
     * @var string
     */
    protected $baseRoutePattern = 'properties-management/properties';

    /**
     * @var string
     */
    protected $baseRouteName = 'admin_erpuserbundle_properties';

    /**
     * @var array
     */
    protected $formOptions = [
        'validation_groups' => ['EditProperty']
    ];

    /**
     * @var FormMapper
     */
    protected $formMapper;

    /**
     * @param string $context
     *
     * @return mixed
     */
    public function createQuery($context = 'list') {
        $query = parent::createQuery($context);
        $query->andWhere($query->getRootAliases()[0] . '.status<>:status')
                ->join($query->getRootAliases()[0] . '.user', 'u', Expr\Join::WITH, 'u.enabled=:enabled')
                ->setParameter('status', Property::STATUS_DELETED)
                ->setParameter('enabled', true)
        ;

        return $query;
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection) {
        $collection
                ->add('removeTenant', $this->getRouterIdParameter() . '/remove-tenant')
                ->remove('export')
                ->remove('delete')
        ;
    }

    /**
     * Add link to sent invitation to complete profile
     *
     * @param MenuItemInterface $menu
     * @param string            $action
     * @param AdminInterface    $childAdmin
     *
     * @return $this|void
     */
    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null) {
        /** @var $property \Erp\PropertyBundle\Entity\Property */
        $property = $this->getSubject();
        if (!$property || $action !== 'edit') {
            return;
        }

        if ($property->getTenantUser()) {
            $textConfirm = 'Are you sure you want to remove this Tenant from Property?'
                    . ' All postponed and recurring payments of this Tenant will be cancelled.';
            $menu->addChild(
                            'Remove Tenant from this Property', ['uri' => $this->generateObjectUrl('removeTenant', $property), 'class' => 'btn red-btn']
                    )
                    ->setAttribute('onclick', 'if (!confirm("' . $textConfirm . '")) return false;');
        }

        return $this;
    }
    
    /**
     * 
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper) {
        $property = $this->getSubject();
        $tenant = $property->getTenantUser();
        $manager = $property->getUser();
        $landlord = $property->getLandlordUser();
        
        $managerAdmin = $this->getConfigurationPool()->getAdminByAdminCode('sonata.page.admin.managers');
        $landlordAdmin = $this->getConfigurationPool()->getAdminByAdminCode('sonata.page.admin.landlords');
        $tenantAdmin = $this->getConfigurationPool()->getAdminByAdminCode('sonata.page.admin.tenants');
        
        $showMapper
                ->tab('Property Info')
                    ->with('General Info', array(
                        'class' => 'col-md-3 col-sm-3'
                    ))
                        ->add('name')
                        ->add('fullAddress', null, array('label' => 'Address'))
                        ->add('price', 'currency', array(
                                'currency' => 'USD',
                                'locale' => 'en_US',
                        ))
                        ->add('createdDate')
                        ->add('paidDate')
                    ->end()
                    ->with('Characteristics', array(
                        'class' => 'col-md-3 col-sm-3'
                    ))
                        ->add('ofBeds', null, array('label' => 'No. of beds'))
                        ->add('ofBaths', null, array('label' => 'No. of baths'))
                        ->add('squareFootage')
                    ->end()
                    ->with('Pictures of the property', array(
                        'class' => 'col-md-6 col-sm-6'
                    ))
                        ->add('images', 'entity', array(
                                'class' => '\Erp\CoreBundle\Entity\Image',
                                'label' => false,
                                'template' => 'ErpAdminBundle:blocks:property_list_images.html.twig'
                        ))
                    ->end()
                    ->with('Appointment Requests', array(
                        'class' => 'col-md-6 col-sm-6'
                    ))
                        ->add('appointmentRequests', 'entity', array(
                                'class' => '\Erp\PropertyBundle\Entity\AppointmentRequest',
                                'label' => false,
                                'template' => 'ErpAdminBundle:blocks:property_list_appointment_requests.html.twig'
                        ))
                    ->end()
                    ->with('Rent History', array(
                        'class' => 'col-md-6 col-sm-6'
                    ))
                        ->add('history', 'entity', array(
                                'class' => '\Erp\PropertyBundle\Entity\PropertyRentHistory',
                                'label' => false,
                                'template' => 'ErpAdminBundle:blocks:property_list_rent_history.html.twig'
                        ))
                    ->end()
                ->end()
        ;
        
        if ($manager) {
            $showMapper
                    ->tab('Manager')
                        ->with('Personal Info', array(
                            'class' => 'col-md-6 col-sm-6'
                        ))
                            ->add('user.firstName', 'text', array('label' => 'First name'))
                            ->add('user.lastName', 'text', array('label' => 'Last name'))
                            ->add('user.email', null, array(
                                    'label' => 'Email address',
                                    'template' => 'ErpAdminBundle:blocks:property_email_user.html.twig',
                                    'route' => array(
                                        'show' => $managerAdmin->generateUrl('show', array('id' => $manager->getId())),
                                        'edit' => $managerAdmin->generateUrl('edit', array('id' => $manager->getId()))
                                    )
                            ))
                            ->add('user.phone', null, array('label' => 'Phone'))
                            ->add('user.fullAddress', 'text', array('label' => 'Address'))
                            ->add('user.createdDate', 'datetime', array('label' => 'Subscription Date'))
                            ->add('user.status', null, array('label' => 'Status'))
                        ->end()
                        ->with('Company', array(
                            'class' => 'col-md-6 col-sm-6'
                        ))
                            ->add('user.companyName', 'text', array('label' => 'Company'))
                            ->add('user.workPhone', null, array('label' => 'Work Phone'))
                            ->add('user.websiteUrl', null, array('label' => 'Website URL'))
                        ->end()
                    ->end()
            ;
        } else {
            $showMapper
                    ->tab('Manager')
                    ->end()
            ;
        }
        
        if ($landlord) {
            $showMapper
                    ->tab('Landlord')
                        ->with('Personal Info', array(
                            'class' => 'col-md-6 col-sm-6'
                        ))
                            ->add('landlordUser.firstName', 'text', array('label' => 'First name'))
                            ->add('landlordUser.lastName', 'text', array('label' => 'First name'))
                            ->add('landlordUser.email', null, array(
                                    'label' => 'Email address',
                                    'template' => 'ErpAdminBundle:blocks:property_email_user.html.twig',
                                    'route' => array(
                                        'show' => $landlordAdmin->generateUrl('show', array('id' => $landlord->getId())),
                                        'edit' => $landlordAdmin->generateUrl('edit', array('id' => $landlord->getId()))
                                    )
                            ))
                            ->add('landlordUser.phone', null, array('label' => 'Phone'))
                            ->add('landlordUser.fullAddress', 'text', array('label' => 'Address'))
                            ->add('landlordUser.createdDate', 'datetime', array('label' => 'Subscription Date'))
                            ->add('landlordUser.status', null, array('label' => 'Status'))
                        ->end()
                        ->with('Company', array(
                            'class' => 'col-md-6 col-sm-6'
                        ))
                            ->add('landlordUser.companyName', 'text', array('label' => 'Company'))
                            ->add('landlordUser.workPhone', null, array('label' => 'Work Phone'))
                            ->add('landlordUser.websiteUrl', null, array('label' => 'Website URL'))
                        ->end()
                    ->end()
            ;
        } else {
            $showMapper
                    ->tab('Landlord')
                    ->end()
            ;
        }
        
        if ($tenant) {
            $showMapper
                    ->tab('Tenant')
                        ->with('Personal Info', array(
                            'class' => 'col-md-6 col-sm-6'
                        ))
                            ->add('tenantUser.firstName', 'text', array('label' => 'First name'))
                            ->add('tenantUser.lastName', 'text', array('label' => 'First name'))
                            ->add('tenantUser.email', null, array(
                                    'label' => 'Email address',
                                    'template' => 'ErpAdminBundle:blocks:property_email_user.html.twig',
                                    'route' => array(
                                        'show' => $tenantAdmin->generateUrl('show', array('id' => $tenant->getId())),
                                        'edit' => $tenantAdmin->generateUrl('edit', array('id' => $tenant->getId()))
                                    )
                            ))
                            ->add('tenantUser.phone', null, array('label' => 'Phone'))
                            ->add('tenantUser.fullAddress', 'text', array('label' => 'Address'))
                            ->add('tenantUser.createdDate', 'datetime', array('label' => 'Subscription Date'))
                            ->add('tenantUser.status', null, array('label' => 'Status'))
                        ->end()
                        ->with('Company', array(
                            'class' => 'col-md-6 col-sm-6'
                        ))
                            ->add('tenantUser.companyName', 'text', array('label' => 'Company'))
                            ->add('tenantUser.workPhone', null, array('label' => 'Work Phone'))
                            ->add('tenantUser.websiteUrl', null, array('label' => 'Website URL'))
                        ->end()
                    ->end()
            ;
        } else {
            $showMapper
                    ->tab('Tenant')
                    ->end()
            ;
        }
    }

    /**
     * @param mixed $object
     *
     * @return $this
     */
    public function prePersist($object) {
        return $this;
    }

    /**
     * @param mixed $object
     *
     * @return $this
     */
    public function preUpdate($object) {
        if ($object->getTenantUser() instanceof User) {
            $this->getConfigurationPool()->getContainer()->get('erp.users.user.service')->activateUser(
                    $object->getTenantUser()
            );
        }

        return $this;
    }

    /**
     * When new administrator created
     *
     * @param mixed $object
     *
     * @return $this
     */
    public function postPersist($object) {
        if ($object instanceof Property) {
            $flashBag = $this->getRequest()->getSession()->getFlashBag();
            $flashBag->set('erp_sonata_flash_success', 'Property has been successfully created.');
        }

        return $this;
    }

    /**
     * When new administrator created
     *
     * @param mixed $object
     *
     * @return $this
     */
    public function postUpdate($object) {
        if ($object instanceof Property) {
            $this->getRequest()->getSession()->getFlashBag()->add(
                    'erp_sonata_flash_success', 'Property has been successfully updated.'
            );
        }
        return $this;
    }

    /**
     * Fields to be shown on filter forms
     *
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper
                ->add('name')
                ->add('user.email', null, array('label' => 'Manager Email'))
                ->add('landlordUser', null, array(), null, array('expanded' => false, 'multiple' => true), array('admin_code' => 'sonata.page.admin.landlords'))
                ->add('tenantUser', null, array(), null, array('expanded' => false, 'multiple' => true), array('admin_code' => 'sonata.page.admin.tenants'))
        ;
    }

    /**
     * Fields to be shown on lists
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper) {
        $listMapper
                ->add('name')
                ->add('city.name', null, ['label' => 'City'])
                ->add('city.stateCode', null, ['label' => 'State'])
                ->add('createdDate')
                ->add('updatedDate')
                ->add('_action', 'actions', array(
                    'actions' => array(
                        'show' => array(
                            'template' => 'ErpAdminBundle:actions:_action_show.html.twig'
                        ),
                        'edit' => array(
                            'template' => 'ErpAdminBundle:actions:_action_edit.html.twig'
                        )
                ))
        );
    }

    /**
     * Fields to be shown on create/edit forms
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper) {
        $this->formMapper = $formMapper;

        $this
                ->addName()
                ->addStateCode()
                ->addCity()
                ->addAddress()
                ->addZip()
                ->addUser()
                ->addLandlordUser()
                ->addTenantUser()
                ->addPrice()
                ->addSquareFootage()
                ->addStatus()
                ->addOfBeds()
                ->addOfBaths()
                ->addAmenities()
                ->addAboutProperties()
                ->addAdditionalDetails()
                ->addImages()
                ->addDocuments()
        ;
    }

    /**
     * @return $this
     */
    private function addName() {
        $this->formMapper->add('name', 'text', ['label' => 'Name']);

        return $this;
    }

    /**
     * @return $this
     */
    private function addAddress() {
        $this->formMapper->add('address', 'text', ['label' => 'Address']);

        return $this;
    }

    /**
     * @return $this
     */
    private function addStateCode() {
        $this->formMapper->add('stateCode', 'choice', array(
            'choices' => $this
                    ->getConfigurationPool()
                    ->getContainer()
                    ->get('erp.core.location')
                    ->getStates(),
            'placeholder' => 'Select state',
            'attr' => ['data-class' => 'states'],
            'label' => 'State'
        ));

        return $this;
    }

    /**
     * @return $this
     */
    private function addCity() {
        $method = $this->getRequest()->getMethod();
        $parameters = $this->getRequest()->request->all();
        $cityId = count($parameters) ? $parameters[$this->getUniqid()]['city'] : null;

        $this->formMapper->add('city', 'entity', array(
            'label' => 'City',
            'class' => 'Erp\CoreBundle\Entity\City',
            'attr' => ['data-class' => 'cities'],
            'query_builder' => function (EntityRepository $er) use ($method, $cityId) {
                if ($method === 'POST') {
                    return $er->createQueryBuilder('c')
                                    ->where('c.id = :cityId')
                                    ->setParameter('cityId', $cityId);
                } else {
                    $stateCode = ($this->id($this->getSubject())) ? $this->getSubject()->getStateCode() : null;

                    return $er->getCitiesByStateCodeQb($stateCode);
                }
            }
        ));

        return $this;
    }

    /**
     * @return $this
     */
    private function addZip() {
        $this->formMapper->add('zip', 'text', array('label' => 'Zip'));

        return $this;
    }

    /**
     * @return $this
     */
    private function addUser() {
        $readonly = (bool) $this->getSubject()->getUser();
        $this->formMapper->add('user', 'entity', array(
            'label' => 'Manager',
            'class' => 'Erp\UserBundle\Entity\User',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                                ->where('u.roles LIKE :roles')
                                ->setParameter('roles', '%"' . User::ROLE_MANAGER . '"%')
                                ->orderBy('u.username', 'ASC');
            },
            'disabled' => $readonly
                ), array('admin_code' => 'sonata.page.admin.managers')
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addLandlordUser() {
        $session = $this->getRequest()->getSession();
        $subject = $this->getSubject();

        $session->set('landlordUser', $subject->getLandlordUser());

        $readonly = ($subject->getId()) ? (bool) ($subject->getLandlordUser() || $subject->getUser()->isReadOnlyUser()) : false
        ;

        $this->formMapper->add('landlordUser', 'entity', array(
            'label' => 'Landlord',
            'class' => 'Erp\UserBundle\Entity\User',
            'query_builder' => function (EntityRepository $er) use ($subject) {
                return $er->createQueryBuilder('u')
                                ->where('u.roles LIKE :roles')
                                ->andWhere('u.enabled=:enabled')
                                ->orWhere('u.id=:userId')
                                ->setParameter('roles', '%"' . User::ROLE_LANDLORD . '"%')
                                ->setParameter('enabled', false)
                                ->setParameter('userId', $this->id($subject->getLandlordUser()))
                                ->orderBy('u.username', 'ASC');
            },
            'placeholder' => 'No Landlord',
            'required' => false,
            'disabled' => $readonly
                ), array('admin_code' => 'sonata.page.admin.tenants')
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addTenantUser() {
        $session = $this->getRequest()->getSession();
        $subject = $this->getSubject();

        $session->set('tenantUser', $subject->getTenantUser());

        $readonly = ($subject->getId()) ? (bool) ($subject->getTenantUser() || $subject->getUser()->isReadOnlyUser()) : false
        ;

        $this->formMapper->add('tenantUser', 'entity', array(
            'label' => 'Tenant',
            'class' => 'Erp\UserBundle\Entity\User',
            'query_builder' => function (EntityRepository $er) use ($subject) {
                return $er->createQueryBuilder('u')
                                ->where('u.roles LIKE :roles')
                                ->andWhere('u.enabled=:enabled')
                                ->orWhere('u.id=:userId')
                                ->setParameter('roles', '%"' . User::ROLE_TENANT . '"%')
                                ->setParameter('enabled', false)
                                ->setParameter('userId', $this->id($subject->getTenantUser()))
                                ->orderBy('u.username', 'ASC');
            },
            'placeholder' => 'No Tenant',
            'required' => false,
            'disabled' => $readonly
                ), array('admin_code' => 'sonata.page.admin.tenants')
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addPrice() {
        $this->formMapper->add('price', 'money', array(
            'label' => 'Price',
            'currency' => 'USD',
        ));

        return $this;
    }

    /**
     * @return $this
     */
    private function addSquareFootage() {
        $this->formMapper->add('squareFootage', 'number', array('label' => 'Square footage'));

        return $this;
    }

    /**
     * @return $this
     */
    private function addStatus() {
        $this->formMapper->add('status', 'choice', array(
            'label' => 'Status',
            'choices' => [
                Property::STATUS_DRAFT => 'Draft (saved, not published)',
                Property::STATUS_AVAILABLE => 'Available (published on the website)',
                Property::STATUS_RENTED => 'Rented (not published)'
            ],
            'preferred_choices' => [Property::STATUS_DRAFT]
        ));

        return $this;
    }

    /**
     * @return $this
     */
    private function addOfBeds() {
        $this->formMapper->add('ofBeds', 'choice', array(
            'choices' => $this
                    ->getConfigurationPool()
                    ->getContainer()
                    ->get('erp.property.service')
                    ->getListOfBeds(),
            'placeholder' => 'No beds',
            'label' => 'Of beds',
            'required' => false
        ));

        return $this;
    }

    /**
     * @return $this
     */
    private function addOfBaths() {
        $this->formMapper->add('ofBaths', 'choice', array(
            'choices' => $this
                    ->getConfigurationPool()
                    ->getContainer()
                    ->get('erp.property.service')
                    ->getListOfBaths(),
            'placeholder' => 'No baths',
            'label' => 'Of baths',
            'required' => false
        ));

        return $this;
    }

    /**
     * @return $this
     */
    private function addAmenities() {
        $this->formMapper->add('amenities', 'textarea', array(
            'label' => 'Amenities',
            'required' => false
        ));

        return $this;
    }

    /**
     * @return $this
     */
    private function addAboutProperties() {
        $this->formMapper->add('aboutProperties', 'textarea', array(
            'label' => 'About properties',
            'required' => false
        ));

        return $this;
    }

    /**
     * @return $this
     */
    private function addAdditionalDetails() {
        $this->formMapper->add('additionalDetails', 'textarea', array(
            'label' => 'Additional details',
            'required' => false
        ));

        return $this;
    }

    /**
     * @return $this
     */
    private function addImages() {
        $this->formMapper->add('images', 'collection', array(
            'type' => new ImageType(),
            'required' => true,
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'by_reference' => false,
            'attr' => [
                'nested_form' => true,
                'nested_form_min' => 1,
            ],
            'label_attr' => [
                'type' => 'images'
            ],
            'label' => 'Property pictures',
            'options' => [
                'label' => 'New picture'
            ],
            'constraints' => [
                new Count(array('min' => 1, 'groups' => ['EditProperty']))
            ]
        ));

        return $this;
    }

    /**
     * @return $this
     */
    private function addDocuments() {
        $this->formMapper->add('documents', 'collection', array(
            'type' => new DocumentType(),
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'by_reference' => false,
            'attr' => [
                'nested_form' => true,
                'nested_form_min' => 0,
            ],
            'label' => 'Property documents',
            'validation_groups' => ['EditProperty'],
            'cascade_validation' => true,
            'options' => [
                'label' => 'New document'
            ],
        ));

        return $this;
    }

}
