<?php

namespace Erp\UserBundle\Services;

use Erp\UserBundle\Entity\User;

class Manager extends UserService {

    /**
     * Get Manager statuses
     *
     * @param bool $withDesc
     *
     * @return array
     */
    public function getStatuses($withDesc = false) {
        $statuses = [User::STATUS_ACTIVE, User::STATUS_PENDING, User::STATUS_NOT_CONFIRMED];
        if ($withDesc) {
            $statuses = [
                User::STATUS_ACTIVE => 'Active (Paid, Confirmed by Admin)',
                User::STATUS_PENDING => 'Pending (Not Paid)',
                User::STATUS_NOT_CONFIRMED => 'Credit Card/Bank authorized (Check transaction status at Stripe and set Active)',
                User::STATUS_DISABLED => 'Disabled',
                User::STATUS_REJECTED => 'Rejected',
            ];
        }

        return $statuses;
    }

    /**
     * Check if Manager`s property has active tenant
     *
     * @param User $manager
     *
     * @return bool
     */
    public function checkIsManagerHasTenants(User $manager) {
        $result = false;
        $properties = $manager->getProperties();
        /** @var $property \Erp\PropertyBundle\Entity\Property */
        foreach ($properties as $property) {
            if ($property->getTenantUser()) {
                $result = true;
            }
        }

        return $result;
    }

}
