<?php

namespace Erp\StripeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BalanceHistory
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Erp\StripeBundle\Entity\BalanceHistoryRepository")
 */
class BalanceHistory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Transaction $transaction
     * @ORM\OneToOne(
     *      targetEntity="\Erp\StripeBundle\Entity\Transaction",
     *      inversedBy="balanceHistory",
     *      cascade={"persist","remove"}
     * )
     * @ORM\JoinColumn(name="transaction_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $transaction;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="string", length=255, nullable=true)
     */
    private $amount;


    /**
     * @var string
     *
     * @ORM\Column(name="balance", type="string", length=255, nullable=true)
     */
    private $balance;


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
     * Get amount
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set amount
     *
     * @param string $amount
     *
     * @return BalanceHistory
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return string
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param string $balance
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
    }

    /**
     * Get document
     *
     * @return Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * Set transaction
     *
     * @param Transaction $document
     *
     * @return BalanceHistory
     */
    public function setTransaction(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

}

