<?php

namespace Erp\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Response;
use Erp\CoreBundle\Entity\CreatedAtAwareTrait;
use Erp\PropertyBundle\Entity\Property;
use Erp\UserBundle\Entity\User;
use \InvalidArgumentException;
use \ReflectionClass;
use \DateTime;

/**
 * Class Remittance
 *
 * @ORM\Table(name="remittances")
 * @ORM\Entity(repositoryClass="Erp\UserBundle\Repository\RemittanceRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Remittance {

    use CreatedAtAwareTrait;

    const TYPE_HOA_FEES = 'HOA Fee';
    const TYPE_REPAIR = 'Repair';
    const TYPE_MANAGEMENT_FEE = 'Management Fee';
    const TYPE_TAXES = 'Taxes';
    const TYPE_BILL = 'Bill';
    const DESCRITION_OTHER = 'Other';
    const REPOSITORY = 'ErpUserBundle:Remittance';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Erp\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="manager_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    protected $manager;

    /**
     * @ORM\ManyToOne(targetEntity="\Erp\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="to_user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    protected $toUser;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="integer", nullable=false)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", nullable=false)
     */
    private $currency;

    /**
     * @var string
     * 
     * @ORM\Column(name="type", type="string", length=255, nullable=false)
     * @Assert\Choice(callback = "getTypeOptions", message = "Invalid selected description", groups={"Remittances"})
     */
    protected $type;

    /**
     * @ORM\OneToOne(targetEntity="\Erp\CoreBundle\Entity\Document", cascade={"persist"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="document_id", referencedColumnName="id", nullable=false)
     */
    protected $document;

    /**
     * @var Property
     *
     * @ORM\ManyToOne(targetEntity="\Erp\PropertyBundle\Entity\Property", inversedBy="transactions")
     * @ORM\JoinColumn(name="property_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    protected $property;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true, length=65535)
     * @Assert\Length(max=65535, maxMessage="Message should have maximum {{ limit }} characters", groups={"Remittances"})
     */
    protected $comment;

    /**
     * 
     * @param string $prefix
     * @return array
     * @throws InvalidArgumentException
     */
    protected static function getConstants($prefix) {
        $oClass = new ReflectionClass(self::class);
        $const = $oClass->getConstants();

        $dump = array();
        foreach ($const as $key => $value) {
            if (substr($key, 0, strlen($prefix)) === $prefix) {
                $dump[] = $value; // $dump[$key] = $value;
            }
        }

        if (empty($dump)) {
            throw new InvalidArgumentException('Bad request: no constants found with prefix ' . $prefix, Response::HTTP_BAD_REQUEST);
        } else {
            return $dump;
        }
    }

    /**
     * 
     * @param boolean $capitalize
     * @return array
     */
    public static function getTypeOptions($capitalize = false) {
        $arr_sex = self::getConstants('TYPE');

        return ($capitalize) ? array_map('ucfirst', $arr_sex) : $arr_sex;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     *
     * @return Remittance
     */
    public function setAmount($amount) {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer
     */
    public function getAmount() {
        return $this->amount;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Remittance
     */
    public function setType($type) {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Remittance
     */
    public function setComment($comment = null) {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment() {
        return $this->comment;
    }

    /**
     * Set toUser
     *
     * @param \Erp\UserBundle\Entity\User $toUser
     *
     * @return Remittance
     */
    public function setToUser(\Erp\UserBundle\Entity\User $toUser) {
        $this->toUser = $toUser;

        return $this;
    }

    /**
     * Get toUser
     *
     * @return \Erp\UserBundle\Entity\User
     */
    public function getToUser() {
        return $this->toUser;
    }

    /**
     * Set document
     *
     * @param \Erp\CoreBundle\Entity\Document $document
     *
     * @return Remittance
     */
    public function setDocument(\Erp\CoreBundle\Entity\Document $document) {
        $this->document = $document;

        return $this;
    }

    /**
     * Get document
     *
     * @return \Erp\CoreBundle\Entity\Document
     */
    public function getDocument() {
        return $this->document;
    }

    /**
     * Set property
     *
     * @param \Erp\PropertyBundle\Entity\Property $property
     *
     * @return Remittance
     */
    public function setProperty(\Erp\PropertyBundle\Entity\Property $property) {
        $this->property = $property;

        return $this;
    }

    /**
     * Get property
     *
     * @return \Erp\PropertyBundle\Entity\Property
     */
    public function getProperty() {
        return $this->property;
    }

    /**
     * Set currency
     *
     * @param string $currency
     *
     * @return Transaction
     */
    public function setCurrency($currency) {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency() {
        return $this->currency;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist() {
        $this->createdAt = new \DateTime();
    }

    /**
     * Set manager
     *
     * @param \Erp\UserBundle\Entity\User $manager
     *
     * @return Remittance
     */
    public function setManager(\Erp\UserBundle\Entity\User $manager) {
        $this->manager = $manager;

        return $this;
    }

    /**
     * Get manager
     *
     * @return \Erp\UserBundle\Entity\User
     */
    public function getManager() {
        return $this->manager;
    }

}
