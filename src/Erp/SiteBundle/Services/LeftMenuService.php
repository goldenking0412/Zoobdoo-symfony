<?php

namespace Erp\SiteBundle\Services;

use Erp\PropertyBundle\Entity\Property;
use Erp\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LeftMenuService
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
    }

    /**
     * @param User $currentUser
     * @param User $anonUser
     * @return \Knp\Menu\ItemInterface|\Knp\Menu\MenuItem
     */
    public function createLeftMenu($currentUser, $anonUser)
    {
        $factory = $this->container->get('knp_menu.factory');
        $menu = $factory->createItem('root', [
            'childrenAttributes' => [
                'class' => 'companions-list',
            ],
        ]);

        if ($currentUser->hasRole(User::ROLE_MANAGER)) {
            $landlords = $currentUser->getLandlords();
            $properties = $currentUser->getPropertiesWithTenants();
            $tenants = [];
            foreach ($properties as $property) {
                $tenants[] = $property->getTenantUser();
            }

            $menu->addChild('Applicants', [
                'route' => 'erp_user_documentation',
                'routeParameters' => ['toUserId' => $anonUser->getId()],
                'attributes' => [
                    'class' => 'companion-name',
                ],
            ]);
            $menu->addChild('Background Checks', [
                'route' => 'erp_smart_move_check_page',
                'attributes' => [
                    'class' => 'companion-name',
                ],
            ]);
            $menu->addChild('Tenants', [
                'childrenAttributes' => [
                    'id' => 'tenants',
                    'class' => 'collapse list-unstyled',
                ],
                'linkAttributes' => [
                    'data-toggle' => 'collapse',
                    'aria-expanded' => 'false',
                ],
                'attributes' => [
                    'class' => 'companion-name',
                ],
                'uri' => '#tenants',
            ]);
            $menu->addChild('Landlords', [
                'childrenAttributes' => [
                    'id' => 'landlords',
                    'class' => 'collapse list-unstyled',
                ],
                'linkAttributes' => [
                    'data-toggle' => 'collapse',
                    'aria-expanded' => 'false',
                ],
                'attributes' => [
                    'class' => 'companion-name',
                ],
                'uri' => '#landlords',
            ]);

            /** @var User $landlord */
            foreach ($landlords as $landlord) {
                $menu['Landlords']->addChild($landlord->getFullName(), [
                    'route' => 'erp_user_documentation',
                    'routeParameters' => ['toUserId' => $landlord->getId()],
                    'extras' => [
                        'total_documents' => $this->getTotalUserDocumentsByToUser($currentUser, $landlord),
                    ],
                ]);
            }

            /** @var User $tenant */
            foreach ($tenants as $tenant) {
                $menu['Tenants']->addChild($tenant->getFullName(), [
                    'route' => 'erp_user_documentation',
                    'routeParameters' => ['toUserId' => $tenant->getId()],
                    'extras' => [
                        'total_documents' => $this->getTotalUserDocumentsByToUser($currentUser, $tenant),
                    ],
                ]);
            }
        } else {

            /** @var Property $property */
            $property = $currentUser->getTenantProperty();

            $managers = [];
            if ($property) {
                $managers = [$property->getUser()];
            }
            $menu->addChild('Managers', [
                'childrenAttributes' => [
                    'id' => 'managers',
                    'class' => 'collapse list-unstyled',
                ],
                'linkAttributes' => [
                    'data-toggle' => 'collapse',
                    'aria-expanded' => 'false',
                ],
                'attributes' => [
                    'class' => 'companion-name',
                ],
                'uri' => '#managers',
            ]);


            /** @var User $manager */
            foreach ($managers as $manager) {
                $menu['Managers']->addChild($manager->getFullName(), [
                    'route' => 'erp_user_documentation',
                    'routeParameters' => ['toUserId' => $manager->getId()],
                    'extras' => [
                        'total_documents' => $this->getTotalUserDocumentsByToUser($manager, $currentUser),
                    ],
                ]);
            }
        }

        return $menu;
    }

    /**
     * Return count documents for user
     *
     * @param User $fromUser
     * @param User $toUser
     *
     * @return int
     */
    private function getTotalUserDocumentsByToUser(User $fromUser, User $toUser) {
        return $this->em->getRepository('ErpUserBundle:UserDocument')
            ->getTotalUserDocumentsByToUser($fromUser, $toUser);
    }
}