<?php

namespace Erp\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as BaseAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

class UnitSettings extends BaseAdmin {

    /**
     * @var string
     */
    protected $baseRoutePattern = 'settings/charge-and-fees';

    /**
     * @var string
     */
    protected $baseRouteName = 'admin_erpuserbundle_charges_and_fees';

    /**
     * @var ProducerInterface
     */
    private $producer;

    public function __construct($code, $class, $baseControllerName, ProducerInterface $producer) {
        $this->producer = $producer;
        parent::__construct($code, $class, $baseControllerName);
    }

    /**
     * @inheritdoc
     */
    protected function configureRoutes(RouteCollection $collection) {
        $collection->remove('delete');
        $collection->clearExcept(['list', 'show', 'edit']);
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $listMapper) {
        $listMapper
                ->add('initialQuantity', 'text', [
                    'label' => 'Initial Quantity'
                ])
                ->add('quantityPerUnit', 'text', [
                    'label' => 'Quantity per unit'
                ])
                ->add('_action', 'actions', [
                    'actions' => [
                        'edit' => []
                    ]
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function configureFormFields(FormMapper $form) {
        $form
                ->add('initialQuantity', 'text', [
                    'label' => 'Initial Quantity'
                ])
                ->add('quantityPerUnit', 'text', [
                    'label' => 'Quantity per unit'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function postUpdate($object) {
        $this->producer->publish(serialize($object));
    }

}
