<?php

namespace Erp\PropertyBundle\Model;

use Erp\CoreBundle\Entity\City;
use Erp\PropertyBundle\Entity\Property;

/**
 * Class PropertyFilter
 *
 * @package Erp\PropertyBundle\Model
 */
class PropertyFilter
{
    const FORM_AVAILABLE_TYPE = 'available_form';
    const FORM_SEARCH_TYPE = 'available_form';

    /**
     * @var string
     */
    protected $state;

    /**
     * @var int
     */
    protected $cityId;

    /**
     * @var City
     */
    protected $city;

    /**
     * @var string
     */
    protected $address;

    /**
     * @var string
     */
    protected $zip;

    /**
     * @var string
     */
    protected $priceMin;

    /**
     * @var string
     */
    protected $priceMax;

    /**
     * @var string
     */
    protected $bathrooms;

    /**
     * @var string
     */
    protected $bedrooms;

    /**
     * @var float
     */
    protected $squareFootage;

    /**
     * @var string
     */
    protected $order = 'price_asc';

    /**
     * @var int
     */
    protected $page = 1;

    /**
     * @var int
     */
    protected $countProperties;

    /**
     * Set state
     *
     * @param string $state
     *
     * @return $this
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set cityId
     *
     * @param int $cityId
     *
     * @return $this
     */
    public function setCityId($cityId)
    {
        $this->cityId = $cityId;

        return $this;
    }

    /**
     * Get cityId
     *
     * @return string
     */
    public function getCityId()
    {
        return $this->cityId;
    }

    /**
     * Set city
     *
     * @param City $city
     *
     * @return $this
     */
    public function setCity(City $city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return $this
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set zip
     *
     * @param string $zip
     *
     * @return $this
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set priceMin
     *
     * @param string $priceMin
     *
     * @return $this
     */
    public function setPriceMin($priceMin)
    {
        $this->priceMin = $priceMin;

        return $this;
    }

    /**
     * Get priceMin
     *
     * @return string
     */
    public function getPriceMin()
    {
        return $this->priceMin;
    }

    /**
     * Set priceMax
     *
     * @param string $priceMax
     *
     * @return $this
     */
    public function setPriceMax($priceMax)
    {
        $this->priceMax = $priceMax;

        return $this;
    }

    /**
     * Get priceMax
     *
     * @return string
     */
    public function getPriceMax()
    {
        return $this->priceMax;
    }

    /**
     * Set bathrooms
     *
     * @param string $bathrooms
     *
     * @return $this
     */
    public function setBathrooms($bathrooms)
    {
        $this->bathrooms = $bathrooms;

        return $this;
    }

    /**
     * Get bathrooms
     *
     * @return string
     */
    public function getBathrooms()
    {
        return $this->bathrooms;
    }

    /**
     * Set bedrooms
     *
     * @param string $bedrooms
     *
     * @return $this
     */
    public function setBedrooms($bedrooms)
    {
        $this->bedrooms = $bedrooms;

        return $this;
    }

    /**
     * Get bedrooms
     *
     * @return string
     */
    public function getBedrooms()
    {
        return $this->bedrooms;
    }

    /**
     * Set squareFootage
     *
     * @param string $squareFootage
     *
     * @return $this
     */
    public function setSquareFootage($squareFootage)
    {
        $this->squareFootage = $squareFootage;

        return $this;
    }

    /**
     * Get squareFootage
     *
     * @return string
     */
    public function getSquareFootage()
    {
        return $this->squareFootage;
    }

    /**
     * Set page
     *
     * @param string $order
     *
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return string
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set page
     *
     * @param int $page
     *
     * @return $this
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set countProperties
     *
     * @param int $countProperties
     *
     * @return $this
     */
    public function setCountProperties($countProperties)
    {
        $this->countProperties = $countProperties;

        return $this;
    }

    /**
     * Get countProperties
     *
     * @return int
     */
    public function getCountProperties()
    {
        return $this->countProperties;
    }

    /**
     * Get countProperties
     *
     * @return int
     */
    public function getTotalPages()
    {
        $result = ceil($this->countProperties / Property::LIMIT_SEARCH_PER_PAGE);

        return $result;
    }
}
