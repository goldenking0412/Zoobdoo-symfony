<?php

namespace Erp\PropertyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Erp\UserBundle\Entity\User;

/**
 * Class UnitSettings
 *
 * @ORM\Table(name="unit_settings")
 * @ORM\Entity(repositoryClass="Erp\PropertyBundle\Repository\UnitSettingsRepository")
 */
class UnitSettings
{
    const DEFAULT_INITIAL_QUANTITY = 125;
    const DEFAULT_QUANTITY_PER_UNIT = 20;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="initial_quantity", type="integer")
     */
    private $initialQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity_per_unit", type="integer")
     */
    private $quantityPerUnit;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set initialQuantity
     *
     * @param integer $initialQuantity
     *
     * @return UnitSettings
     */
    public function setInitialQuantity($initialQuantity)
    {
        $this->initialQuantity = (int) $initialQuantity;

        return $this;
    }

    /**
     * Get initialQuantity
     *
     * @return integer
     */
    public function getInitialQuantity()
    {
        return $this->initialQuantity;
    }

    /**
     * Set quantityPerUnit
     *
     * @param integer $quantityPerUnit
     *
     * @return UnitSettings
     */
    public function setQuantityPerUnit($quantityPerUnit)
    {
        $this->quantityPerUnit = (int) $quantityPerUnit;

        return $this;
    }

    /**
     * Get quantityPerUnit
     *
     * @return integer
     */
    public function getQuantityPerUnit()
    {
        return $this->quantityPerUnit;
    }
}
