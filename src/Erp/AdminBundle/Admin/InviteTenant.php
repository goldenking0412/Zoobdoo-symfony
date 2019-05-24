<?php

namespace Erp\AdminBundle\Admin;

use Erp\PropertyBundle\Entity\Property;
use Erp\UserBundle\Entity\InvitedUser;
use Erp\UserBundle\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin as BaseAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;

/**
 * Class InviteTenant
 *
 * @package Erp\AdminBundle\Admin
 */
class InviteTenant extends BaseAdmin
{
    protected $baseRoutePattern = 'user-mangement/tenants/invite-tenant';

    protected $baseRouteName = 'admin_erpuserbundle_invite_tenant';

    protected $formOptions = [
        'validation_groups' => ['InvitedUser']
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
    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        $query->andWhere($query->getRootAliases()[0] . '.isUse LIKE :isUse')
            ->setParameter('isUse', false);

        return $query;
    }

    /**
     * @param mixed $object
     *
     * @return $this
     */
    public function preRemove($object)
    {
        $object->getProperty()->setStatus(Property::STATUS_AVAILABLE);

        return $this;
    }

    /**
     * @param mixed $object
     *
     * @return $this
     */
    public function prePersist($object)
    {
        if ($object instanceof InvitedUser) {
            $container = $this->getConfigurationPool()->getContainer();

            $inviteCode = $container->get('fos_user.util.token_generator')->generateToken();

            $object->setInviteCode($inviteCode);
            $object->getProperty()->setStatus(Property::STATUS_RENTED);
        }

        return $this;
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list', 'create', 'delete']);
        $collection->remove('export');
        $collection->remove('batch');
    }

    /**
     * @return array
     */
    public function getBatchActions()
    {
        $actions = parent::getBatchActions();
        unset($actions['delete']);

        return $actions;
    }

    /**
     * Fields to be shown on create/edit forms
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $this->formMapper = $formMapper;
        $this->addProperty();
        $this->formMapper->add('invitedEmail', 'text');
    }

    /**
     * Fields to be shown on lists
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('property')
            ->add('invitedEmail', 'text')
            ->add('_action', 'actions', ['actions' => ['delete' => []]]);
    }

    /**
     * @return $this
     */
    private function addProperty()
    {
        $this->formMapper->add(
            'property',
            'entity',
            [
                'class' => 'Erp\PropertyBundle\Entity\Property',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.status IN(:statuses)')
                        ->join('p.user', 'u', Expr\Join::WITH, 'u.status=:user_status_active')
                        ->andWhere('p.tenantUser IS NULL')
                        ->setParameter('user_status_active', User::STATUS_ACTIVE)
                        ->setParameter('statuses', [Property::STATUS_AVAILABLE])
                        ->orderBy('p.name', 'ASC');
                },
                'placeholder' => 'Select property',
                'required' => true
            ]
        );

        return $this;
    }
}
