<?php

namespace Erp\NotificationBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

class EntityToIntTransformer implements DataTransformerInterface {

    /** @var \Doctrine\Common\Persistence\ObjectManager */
    protected $om;
    protected $entityClass;
    protected $entityRepository;
    protected $entityType;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om) {
        $this->om = $om;
    }

    /**
     * This function transforms the entity specified by $this->entityClass into
     * an $id which can be used within the form
     * http://symfony.com/doc/2.8/cookbook/form/data_transformers.html#harder-example-transforming-an-issue-number-into-an-issue-entity
     * 
     * @param mixed $entity
     *
     * @return integer | array
     */
    public function transform($entity) {
        /*
         * Modified from comments to use instanceof so that base classes or
         * interfaces can be specified
         */
        if (null === $entity || !($entity instanceof $this->entityClass)) {
            return '';
        }

        return $entity->getId();
    }

    /**
     * This function transforms the submitted $id of the form to an entity specified by
     * $this->entityClass
     * http://symfony.com/doc/2.8/cookbook/form/data_transformers.html#harder-example-transforming-an-issue-number-into-an-issue-entity
     * 
     * @param mixed $id
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     *
     * @return mixed | object | array
     */
    public function reverseTransform($id) {
        if (!$id) {
            return null;
        }

        $entity = $this->om->getRepository($this->entityRepository)->findOneBy(array('id' => $id));

        if (null === $entity) {
            throw new TransformationFailedException(sprintf(
                    'A %s with id (%s) does not exist!', $this->entityType, $id
            ));
        }

        return $entity;
    }

    /**
     * Set the $entityClass property
     * 
     * @param string $entityClass
     */
    public function setEntityClass($entityClass) {
        $this->entityClass = $entityClass;
    }

    /**
     * Set the $entityRepository property
     * 
     * @param string $entityRepository
     */
    public function setEntityRepository($entityRepository) {
        $this->entityRepository = $entityRepository;
    }

    public function setEntityType($entityType) {
        $this->entityType = $entityType;
    }

}
