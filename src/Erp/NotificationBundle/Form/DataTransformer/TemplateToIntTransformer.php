<?php

namespace Erp\NotificationBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Erp\NotificationBundle\Entity\Template;

class TemplateToIntTransformer extends EntityToIntTransformer {

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om) {
        parent::__construct($om);
        $this->setEntityClass(Template::class);
        $this->setEntityRepository(Template::REPOSITORY);
        $this->setEntityType("template");
    }

}
