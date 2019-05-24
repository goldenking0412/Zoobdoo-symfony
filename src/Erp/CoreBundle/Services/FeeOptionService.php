<?php

namespace Erp\CoreBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;

class FeeOptionService {

    /**
     *
     * @var EntityManager
     */
    protected $em;

    /**
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(EntityManager $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * Get fees
     *
     * @return null|\Erp\CoreBundle\Entity\FeeOption
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getFees() {
        $repository = $this->em->getRepository(\Erp\CoreBundle\Entity\FeeOption::REPOSITORY);
        $fees = $repository
                ->createQueryBuilder('f')
                ->getQuery()
                ->getOneOrNullResult()
        ;

        return $fees;
    }

    /**
     * Get ErentPay mounthly fee
     *
     * @return float|int
     */
    public function getErentpayFee() {
        $amount = 0;
        $fees = $this->getFees();
        if ($fees) {
            $amount = $fees->getErentpay();
        }

        return $amount;
    }

    /**
     * Get rent allowance amount
     *
     * @return float|int
     */
    public function getRentAllowanceAmount() {
        $amount = 0;
        $fees = $this->getFees();
        if ($fees) {
            $amount = $fees->getRentAllowance();
        }

        return $amount;
    }

    /**
     * Get system email
     *
     * @return string
     */
    public function getSystemEmail() {
        $email = 'info@zoobdoo.com ';
        $fees = $this->getFees();
        if ($fees) {
            $email = $fees->getDefaultEmail();
        }

        return $email;
    }

    /**
     * Get ErentPay Tenant Screening Fee
     *
     * @return float|int
     */
    public function getTenantScreeningFee() {
        $amount = 1;
        $fees = $this->getFees();
        if ($fees) {
            $amount = $fees->getBackgroundCheck();
        }

        return $amount;
    }

    /**
     * Get eRentPay Property Fee
     *
     * @return float|int
     */
    public function getPropertyFee() {
        $amount = 1;

        $fees = $this->getFees();
        if ($fees) {
            $amount = $fees->getPropertyFee();
        }

        return $amount;
    }

    /**
     * Get eRentPay - Post Vacancy Online Fee
     *
     * @return float|int
     */
    public function getPostVacancyOnlineFee() {
        $amount = 1;

        $fees = $this->getFees();
        if ($fees) {
            $amount = $fees->getPostVacancyOnlineFee();
        }

        return $amount;
    }

    /**
     * Get eRentPay - Create Application Form Fee
     *
     * @return float|int
     */
    public function getCreateApplicationFormFee() {
        $amount = 1;

        $fees = $this->getFees();
        if ($fees) {
            $amount = $fees->getCreateApplicationFormFee();
        }

        return $amount;
    }

    /**
     * Get eRentPay - Create Contract Form Fee
     *
     * @return float|int
     */
    public function getCreateContractFormFee() {
        $amount = 1;

        $fees = $this->getFees();
        if ($fees) {
            $amount = $fees->getCreateContractFormFee();
        }

        return $amount;
    }

    /**
     * Get eRentPay - SmartMove Enable
     *
     * @return bool
     */
    public function getSmartMoveEnable() {
        $value = 0;

        $fees = $this->getFees();
        if ($fees) {
            $value = $fees->getSmartMoveEnable();
        }

        return $value;
    }

    /**
     * Get eRentPay - Check Payment
     *
     * @return float|int
     */
    public function getCheckPaymentFee() {
        $value = 1;

        $fees = $this->getFees();
        if ($fees) {
            $value = $fees->getCheckPaymentFee();
        }

        return $value;
    }

    /**
     * Get eRentPay CCard Transaction Fee (%)
     *
     * @return float|int
     */
    public function getCcTransactionFee() {
        $value = 0;

        $fees = $this->getFees();
        if ($fees) {
            $value = $fees->getCcTransactionFee();
        }

        return $value;
    }

    /**
     * Get eRentPay ACH Transaction Fee (USD)
     *
     * @return float|int
     */
    public function getAchTransactionFee() {
        $value = 0;

        $fees = $this->getFees();
        if ($fees) {
            $value = $fees->getAchTransactionFee();
        }

        return $value;
    }

    /**
     * @return float|int
     */
    public function getApplicationFormAnonymousFee() {
        $value = 1;

        $fees = $this->getFees();
        if ($fees) {
            $value = $fees->getApplicationFormAnonymousFee();
        }

        return $value;
    }

}
