<?php

namespace Erp\SmartMoveBundle\Models;

use Erp\UserBundle\Entity\User;
use Erp\SmartMoveBundle\Entity\SmartMoveRenter;

/**
 * Class ManagerAPIModel
 *
 * @package Erp\SmartMoveBundle\Models
 */
class ManagerAPIModel implements SmartMoveModelInterface
{
    /**
     * @var User
     */
    protected $manager = null;

    /**
     * @var SmartMoveRenter
     */
    protected $smRenter = null;

    /**
     * @var string
     */
    protected $email = null;

    /**
     * Set tenant
     *
     * @param User $manager
     *
     * @return $this
     */
    public function setManager(User $manager)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * Get tenant
     *
     * @return User|null
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Set smRenter
     *
     * @param SmartMoveRenter $smRenter
     *
     * @return $this
     */
    public function setSMRenter(SmartMoveRenter $smRenter)
    {
        $this->smRenter = $smRenter;

        return $this;
    }

    /**
     * Get smRenter
     *
     * @return SmartMoveRenter|null
     */
    public function getSMRenter()
    {
        return $this->smRenter;
    }

    /**
     * Set tenant
     *
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get tenant
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
}
