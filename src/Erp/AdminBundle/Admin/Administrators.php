<?php

namespace Erp\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as BaseAdmin;
use Erp\UserBundle\Entity\User;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use FOS\UserBundle\Model\UserInterface;
use Doctrine\ORM\EntityRepository;

class Administrators extends BaseAdmin {

    protected $baseRouteName = 'admin_erpuserbundle_administrators';
    protected $baseRoutePattern = 'user-mangement/administrators';
    protected $formOptions = [
        'validation_groups' => ['AdminCreated']
    ];

    /**
     * Default Datagrid values
     *
     * @var array
     */
    protected $datagridValues = [
        '_page' => 1,
        '_sort_by' => 'email'
    ];

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection) {
        $collection->add('disableAdmin', $this->getRouterIdParameter() . '/disable-admin');
        $collection->remove('export');
        $collection->remove('delete');
    }

    /**
     * @param string $context
     *
     * @return mixed
     */
    public function createQuery($context = 'list') {
        $query = parent::createQuery($context);
        $query->andWhere($query->getRootAliases()[0] . '.roles LIKE :roles')
                ->setParameter('roles', '%"' . User::ROLE_ADMIN . '"%');

        return $query;
    }

    /**
     * When new administrator created
     *
     * @param mixed $object
     */
    public function prePersist($object) {
        if ($object instanceof UserInterface) {
            $this->getConfigurationPool()->getContainer()->get('erp.users.administrator_service')->onNewAdmin($object);
        }
    }

    /**
     * Fields to be shown on lists
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper) {
        $statuses = $this->getConfigurationPool()->getContainer()->get('erp.users.administrator_service')->getStatuses(true);

        $listMapper
                ->add('firstName')
                ->add('lastName')
                ->addIdentifier('email')
                ->add('status', 'choice', ['choices' => $statuses, 'required' => true])
                ->add('createdDate')
                ->add('lastLogin')
        ;
    }

    /**
     * Fields to be shown on lists
     *
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper) {
        $statuses = $this->getConfigurationPool()->getContainer()->get('erp.users.administrator_service')->getStatuses(true);

        $showMapper
                ->add('firstName')
                ->add('lastName')
                ->addIdentifier('email')
                ->add('status', 'choice', ['choices' => $statuses, 'required' => true])
                ->add('createdDate')
                ->add('lastLogin')
        ;
    }

    /**
     * Fields to be shown on create/edit forms
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper) {
        $statuses = $this->getConfigurationPool()->getContainer()->get('erp.users.administrator_service')->getStatuses(true);

        if ($this->id($this->getSubject())) {
            $user = $this->getSubject();

            $isDisabled = false;
            if ($user->getStatus() == User::STATUS_DISABLED) {
                $isDisabled = true;
            }

            if ($user->getId()) {
                $isDisabledEmail = true;
            } else {
                $isDisabledEmail = false;
            }

            $formMapper
                    ->add('firstName', 'text', ['label' => 'First Name', 'required' => false, 'disabled' => $isDisabled])
                    ->add('lastName', 'text', ['label' => 'Last Name', 'required' => false, 'disabled' => $isDisabled])
                    ->add('email', 'email', ['label' => 'Email', 'disabled' => $isDisabledEmail])
                    ->add('status', 'choice', ['choices' => $statuses, 'required' => false, 'disabled' => true]);
        } else {
            $formMapper->add('email', 'email', ['label' => 'Email']);
        }
    }

    /**
     * Fields to be shown on filter forms
     *
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper
                ->add('email')
                ->add('firstName')
                ->add('lastName');
    }

    /**
     * @param MenuItemInterface   $menu
     * @param                     $action
     * @param AdminInterface|null $childAdmin
     *
     * @return $this|void
     */
    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null) {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getSubject();
        if (!$user || $action !== 'edit') {
            return;
        }

        switch ($user->getStatus()) {
            case User::STATUS_ACTIVE:
                $textConfirm = 'Are you sure you want to disable this Admin\'s account? ';

                $menu->addChild(
                                'Disable Admin', ['uri' => $this->generateObjectUrl('disableAdmin', $user), 'class' => 'btn red-btn']
                )
                        ->setAttribute('onclick', 'if (!confirm("' . $textConfirm . '")) return false;');

                break;
        }

        return $this;
    }

}
