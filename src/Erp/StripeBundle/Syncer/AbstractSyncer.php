<?php

namespace Erp\StripeBundle\Syncer;

abstract class AbstractSyncer implements SyncerInterface
{
    /**
     * @var InvoiceSyncer
     */
    private $invoiceSyncer;

    /**
     * @var ChargeSyncer
     */
    private $chargeSyncer;

    public function getInvoiceSyncer()
    {
        return $this->invoiceSyncer;
    }

    public function setInvoiceSyncer($invoiceSyncer)
    {
        $this->invoiceSyncer = $invoiceSyncer;
    }

    public function setChargeSyncer($chargeSyncer)
    {
        $this->chargeSyncer = $chargeSyncer;
    }

    public function getChargeSyncer()
    {
        return $this->chargeSyncer;
    }
}
