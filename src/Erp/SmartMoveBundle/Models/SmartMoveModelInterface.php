<?php

namespace Erp\SmartMoveBundle\Models;

/**
 * Interface SmartMoveModelInterface
 *
 * @package Erp\SmartMoveBundle\Models
 */
interface SmartMoveModelInterface
{
    /**
     * @return \Erp\SmartMoveBundle\Entity\SmartMoveRenter
     */
    public function getSMRenter();
}
