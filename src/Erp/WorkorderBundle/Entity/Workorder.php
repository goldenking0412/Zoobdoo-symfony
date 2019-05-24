<?php

namespace Erp\WorkorderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;
use \ReflectionClass;
use \InvalidArgumentException;

/**
 * Edit
 *
 * @ORM\Table(name="workorder")
 * @ORM\Entity(repositoryClass="Erp\WorkorderBundle\Repository\WorkorderRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Workorder {
    
    const REPOSITORY = 'ErpWorkorderBundle:Workorder';
    
    const STATUS_COMPLETED = 'Complete';
    const STATUS_PROCESSING = 'Processing';
    const STATUS_CREATED = 'Created';
    
    const PRIORITY_NORMAL = 'Normal';
    const PRIORITY_URGENT = 'Urgent';
    const PRIORITY_EMERGENCY = 'Emergency';
    
    const SEVERITY_MINOR = 'Minor';
    const SEVERITY_MODERATE = 'Moderate';
    const SEVERITY_MAJOR = 'Major';
    const SEVERITY_SEVERE = 'Severe';
    
    const CURRENCY_USD = 'USD';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime")
     */
    private $createdDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Erp\WorkorderBundle\Entity\WorkorderService", mappedBy="workorder", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $services;

    /**
     * @var integer
     *
     * @ORM\Column(name="contractor_id", type="integer")
     */
    private $contractor;

    /**
     * @var integer
     *
     * @ORM\Column(name="manager_id", type="integer")
     */
    private $manager;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=255)
     */
    private $currency;

    /**
     * @var integer
     *
     * @ORM\Column(name="severity", type="integer")
     */
    private $severity;

    /**
     * @var integer
     *
     * @ORM\Column(name="urgency", type="integer")
     */
    private $urgency;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=512)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="service_date", type="datetime")
     */
    private $serviceDate;

    /**
     * @var string
     *
     * @ORM\Column(name="service_time", type="string", length=255)
     */
    private $serviceTime;

    /**
     * Constructor
     */
    public function __construct() {
        $this->services = new ArrayCollection();
    }

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
    public static function getStatusOptions($capitalize = false) {
        $arr_sex = self::getConstants('STATUS');

        return ($capitalize) ? array_map('ucfirst', $arr_sex) : $arr_sex;
    }

    /**
     * 
     * @param boolean $capitalize
     * @return array
     */
    public static function getPriorityOptions($capitalize = false) {
        $arr_sex = self::getConstants('PRIORITY');

        return ($capitalize) ? array_map('ucfirst', $arr_sex) : $arr_sex;
    }

    /**
     * 
     * @param boolean $capitalize
     * @return array
     */
    public static function getCurrencyOptions($capitalize = false) {
        $arr_sex = self::getConstants('CURRENCY');

        return ($capitalize) ? array_map('ucfirst', $arr_sex) : $arr_sex;
    }

    /**
     * 
     * @param boolean $capitalize
     * @return array
     */
    public static function getSeverityOptions($capitalize = false) {
        $arr_sex = self::getConstants('SEVERITY');

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
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return Workorder
     */
    public function setCreatedDate($createdDate) {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime
     */
    public function getCreatedDate() {
        return $this->createdDate;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Workorder
     */
    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus() {
        return $this->status;
    }
    
    /**
     * 
     * @return string
     */
    public function getStatusAsString() {
        $statuses = self::getStatusOptions();
        
        return $statuses[$this->status];
    }

    /**
     * Add service
     *
     * @param WorkorderService $service
     *
     * @return Workorder
     */
    public function addService(WorkorderService $service) {
        $this->services->add($service);

        return $this;
    }

    /**
     * Remove service
     *
     * @param WorkorderService $service
     */
    public function removeAlert(WorkorderService $service) {
        $this->services->removeElement($service);
    }

    /**
     * Get services
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getServices() {
        return $this->services;
    }

    /**
     * Set contractor
     *
     * @param integer $contractor
     *
     * @return Workorder
     */
    public function setContractor($contractor) {
        $this->contractor = $contractor;

        return $this;
    }

    /**
     * Get contractor
     *
     * @return integer
     */
    public function getContractor() {
        return $this->contractor;
    }

    /**
     * Set manager
     *
     * @param integer $manager
     *
     * @return Workorder
     */
    public function setManager($manager) {
        $this->manager = $manager;

        return $this;
    }

    /**
     * Get manager
     *
     * @return integer
     */
    public function getManager() {
        return $this->manager;
    }

    /**
     * Set currency
     *
     * @param string $currency
     *
     * @return Workorder
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
     * Set severity
     *
     * @param integer $severity
     *
     * @return Workorder
     */
    public function setSeverity($severity) {
        $this->severity = $severity;

        return $this;
    }

    /**
     * Get severity
     *
     * @return integer
     */
    public function getSeverity() {
        return $this->severity;
    }
    
    /**
     * 
     * @return string
     */
    public function getSeverityAsString() {
        $severities = self::getSeverityOptions();
        
        return $severities[$this->severity];
    }

    /**
     * Set urgency
     *
     * @param integer $urgency
     *
     * @return Workorder
     */
    public function setUrgency($urgency) {
        $this->urgency = $urgency;

        return $this;
    }

    /**
     * Get urgency
     *
     * @return integer
     */
    public function getUrgency() {
        return $this->urgency;
    }
    
    /**
     * 
     * @return string
     */
    public function getUrgencyAsString() {
        $urgencies = self::getPriorityOptions();
        
        return $urgencies[$this->urgency];
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Workorder
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set serviceDate
     *
     * @param \DateTime $serviceDate
     *
     * @return Workorder
     */
    public function setServiceDate($serviceDate) {
        $this->serviceDate = $serviceDate;

        return $this;
    }

    /**
     * Get serviceDate
     *
     * @return \DateTime
     */
    public function getServiceDate() {
        return $this->serviceDate;
    }

    /**
     * Set serviceTime
     *
     * @param string $serviceTime
     *
     * @return Workorder
     */
    public function setServiceTime($serviceTime) {
        $this->serviceTime = $serviceTime;

        return $this;
    }

    /**
     * Get serviceTime
     *
     * @return string
     */
    public function getServiceTime() {
        return $this->serviceTime;
    }

}
