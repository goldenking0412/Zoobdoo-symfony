<?php

namespace Erp\AdminBundle\Admin;

use Erp\UserBundle\Entity\User;
use Erp\PropertyBundle\Entity\Property;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use FOS\UserBundle\Model\UserInterface;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Route\RouteCollection;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

/**
 * Class Tenants
 * @package Erp\AdminBundle\Admin
 */
class Tenants extends Administrators {

    protected $baseRouteName = 'admin_erpuserbundle_tenants';
    protected $baseRoutePattern = 'user-management/tenants';
    protected $formOptions = [
        'validation_groups' => ['AdminCreated']
    ];

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection) {
        $collection->clearExcept(['list', 'edit', 'show']);
        $collection->add('deleteTenant', $this->getRouterIdParameter() . '/delete-tenant');
    }

    /**
     * Default Datagrid values
     *
     * @var array
     */
    protected $datagridValues = [
        '_page' => 1,
        '_sort_by' => 'status'
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
        $query->andWhere($query->getRootAliases()[0] . '.roles LIKE :roles')
                ->andWhere($query->getRootAliases()[0] . '.status NOT IN (:statuses)')
                ->setParameter('roles', '%"' . User::ROLE_TENANT . '"%')
                ->setParameter('statuses', [User::STATUS_DELETED])
        ;

        return $query;
    }

    /**
     * When new tenant created
     *
     * @param mixed $object
     *
     * @return $this
     */
    public function prePersist($object) {
        if ($object instanceof UserInterface) {
            $object->setUsername($object->getEmail())
                    ->setRoles([User::ROLE_TENANT]);
        }

        return $this;
    }

    /**
     * @param mixed $object
     *
     * @return $this
     */
    public function postUpdate($object) {
        if ($object instanceof UserInterface) {
            if (!$object->isEnabled()) {
                $tenantProperty = $object->getTenantProperty();
                if ($tenantProperty) {
                    /** @var Property $tenantProperty */
                    $tenantProperty->setTenantUser(null);
                    $this->getConfigurationPool()
                            ->getContainer()
                            ->get('erp.property.service')
                            ->detachTenant($tenantProperty);
                }
            }
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
                ->add('status')
                ->add('email')
                ->add('firstName')
                ->add('lastName')
                ->add('state')
        ;
    }


    /**
     * Fields to be shown on create/edit forms
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper) {
        $this->formMapper = $formMapper;
        $this->formMapper
                ->add('firstName', 'text', ['label' => 'First Name'])
                ->add('lastName', 'text', ['label' => 'Last Name'])
                ->add('email', 'email', ['label' => 'Email', 'disabled' => true])
                ->add('phone', 'text', ['label' => 'Phone Number'])
                ->add('websiteUrl', 'text', ['label' => 'Website', 'required' => false])
                ->add('addressOne', 'text', ['label' => 'Address', 'required' => true])
                ->add('addressTwo', 'text', ['label' => 'Address 2', 'required' => false]);

        $this
                ->addState()
                ->addCity();

        $this->formMapper->add('postalCode', 'text', ['label' => 'Zip']);

        if (!$this->id($this->getSubject())) {
            $this->formMapper->add('plainPassword', 'repeated', [
                'type' => 'password',
                'options' => ['translation_domain' => 'FOSUserBundle'],
                'first_options' => ['label' => 'form.password'],
                'second_options' => ['label' => 'form.password_confirmation'],
                'invalid_message' => 'fos_user.password.mismatch',
            ]);
        }
    }

    /**
     * @return $this
     */
    private function addState() {
        $this->formMapper->add('state', 'choice', [
            'choices' => $this
                    ->getConfigurationPool()
                    ->getContainer()
                    ->get('erp.core.location')
                    ->getStates(),
            'placeholder' => 'Select state',
            'attr' => ['data-class' => 'states'],
            'label' => 'State'
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    private function addCity() {
        $method = $this->getRequest()->getMethod();
        $parameters = $this->getRequest()->request->all();
        $cityId = count($parameters) ? $parameters[$this->getUniqid()]['city'] : null;

        $this->formMapper->add('city', 'entity', [
            'label' => 'City',
            'class' => 'Erp\CoreBundle\Entity\City',
            'attr' => ['data-class' => 'cities'],
            'query_builder' => function (EntityRepository $er) use ($method, $cityId) {
                if ($method === 'POST') {
                    return $er->createQueryBuilder('c')
                                    ->where('c.id = :cityId')
                                    ->setParameter('cityId', $cityId);
                } else {
                    $stateCode = ($this->id($this->getSubject())) ? $this->getSubject()->getState() : null;

                    return $er->getCitiesByStateCodeQb($stateCode);
                }
            }
        ]);

        return $this;
    }

    /**
     * Fields to be shown on lists
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper) {
        parent::configureListFields($listMapper);
        $listMapper
                ->add('tenantProperty')
                ->add('isTermOfUse', null, ['label' => 'Terms Of Use'])
                ->add('_action', 'actions', array(
                    'actions' => array(
                        'show' => array(
                            'template' => 'ErpAdminBundle:actions:_action_show.html.twig'
                        ),
                        'edit' => array(
                            'template' => 'ErpAdminBundle:actions:_action_edit.html.twig'
                        )
                    )
                ))
        ;
    }

    /**
     * @param MenuItemInterface   $menu
     * @param                     $action
     * @param AdminInterface|null $childAdmin
     *
     * @return $this
     */
    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null) {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getSubject();
        if (!$user || $action !== 'edit') {
            return;
        }

        switch ($user->getStatus()) {
            case User::STATUS_REJECTED:
            case User::STATUS_DISABLED:
                $textConfirm = 'Are you sure you want to delete this account? ALL USER DATA WILL BE DELETED: ';
                $textConfirm .= 'messages, documents, payment history, etc!';

                $menu->addChild(
                                'Delete tenant', ['uri' => $this->generateObjectUrl('deleteTenant', $user), 'class' => 'btn red-btn']
                        )
                        ->setAttribute('onclick', 'if (!confirm("' . $textConfirm . '")) return false;');

                break;

            default:
                $textAlert = 'To delete Tenant\'s account first you have to remove this Tenant from the property.';

                $menu->addChild(
                                'Delete tenant', ['uri' => '#', 'class' => 'btn red-btn']
                        )
                        ->setAttribute('onclick', 'alert("' . $textAlert . '"); return false;');

                break;
        }


        return $this;
    }

    /**
     * 
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper) {
        $showMapper
                ->tab('General Info')
                    ->with('Personal Info', array(
                        'class' => 'col-md-6 col-sm-6'
                    ))
                        ->add('firstName')
                        ->add('lastName')
                        ->add('phone')
                        ->add('fullAddress', 'text', array('label' => 'Address'))
                        ->add('createdDate', 'datetime', array('label' => 'Subscription Date'))
                        ->add('status')
                    ->end()
                    ->with('Company', array(
                       'class' => 'col-md-6 col-sm-6'
                    ))
                        ->add('companyName')
                        ->add('workPhone')
                        ->add('websiteUrl')
                    ->end()
                    ->with('Properties', array(
                        'class' => 'col-md-12 col-sm-12 ',
                        'collapsed' => true
                    ))
                        ->add('properties', 'entity', array(
                            'class' => '\Erp\PropertyBundle\Entity\Property',
                            'label' => false,
                            'template' => 'ErpAdminBundle:blocks:manager_list_properties.html.twig'
                    ))
                    ->end()
                ->end()
                ->tab('Stripe Account')
                    ->with('Registration Info', array(
                        'class' => 'col-md-6 col-sm-6'
                    ))
                        ->add('stripeAccount.firstName', 'text', array('label' => 'Registered First Name'))
                        ->add('stripeAccount.lastName', 'text', array('label' => 'Registered Last Name'))
                        ->add('stripeAccount.birthday', 'text', array('label' => 'Registered Birthday'))
                        ->add('stripeAccount.addressToString', 'text', array('label' => 'Registered address'))
                    ->end()
                    ->with('Account Info', array(
                        'class' => 'col-md-6 col-sm-6'
                    ))
                        ->add('stripeAccount.accountId', 'text', array('label' => 'Account ID'))
                        ->add('stripeAccount.businessName', 'text', array('label' => 'Business Name'))
                        ->add('stripeAccount.businessTaxId', 'text', array('label' => 'Business Tax ID'))
                        ->add('stripeAccount.ssnLast4', 'text', array('label' => 'SSN Last Four Digits'))
                    ->end()
                ->end()
        ;
    }

}
