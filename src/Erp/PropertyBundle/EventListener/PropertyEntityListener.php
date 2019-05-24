<?php

namespace Erp\PropertyBundle\EventListener;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Erp\PropertyBundle\Entity\Property;
use Erp\PropertyBundle\Entity\PropertyRentHistory;
use Doctrine\Common\Persistence\ManagerRegistry;

class PropertyEntityListener {

    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * @var bool
     */
    private $statusChanged = false;

    /**
     * @var Property
     */
    private $property;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry) {
        $this->registry = $registry;
    }

    /**
     * @param Property $property
     */
    public function postPersist(Property $property) {
        $this->createHistoryRecord($property);
    }

    /**
     * @param Property $property
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(Property $property, PreUpdateEventArgs $args) {
        if ($args->hasChangedField(Property::FILED_STATUS)) {
            $this->statusChanged = true;
            $this->property = $property;
        }
    }

    /**
     * @param PostFlushEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $args) {
        if ($this->statusChanged && $this->property) {
            $property = $this->property;
            $this->clearStatusDetection();
            $this->createHistoryRecord($property);
        }
    }

    private function clearStatusDetection() {
        $this->statusChanged = false;
        $this->property = null;
    }

    /**
     * @param Property $property
     */
    private function createHistoryRecord(Property $property) {
        $propertyRentHistory = new PropertyRentHistory();
        $propertyRentHistory->setStatus($property->getStatus())
                ->setProperty($property);

        $em = $this->registry->getManagerForClass(PropertyRentHistory::class);
        $em->persist($propertyRentHistory);
        $em->flush();
    }

}
