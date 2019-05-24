<?php

namespace Erp\PropertyBundle\Entity;

class Unit
{
    /**
     * @var integer
     */
    private $quantity;

    /**
     * Set quantity
     *
     * @param $quantity
     *
     * @return $this
     */
    public function setQuantity($quantity)
    {
        $this->quantity = (int) $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
}