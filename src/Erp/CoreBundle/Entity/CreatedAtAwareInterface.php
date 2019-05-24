<?php

namespace Erp\CoreBundle\Entity;

interface CreatedAtAwareInterface
{
    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param \DateTime $createdAt
     * @return mixed
     */
    public function setCreatedAt(\DateTime $createdAt = null);
}