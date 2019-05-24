<?php

namespace Erp\SmartMoveBundle\Managers;

use Erp\SmartMoveBundle\Models\SmartMoveModelInterface;

/**
 * Interface SmartMoveManagerInterface
 *
 * @package Erp\SmartMoveBundle\Managers
 */
interface SmartMoveManagerInterface
{
    const STATUS_OK    = 'ok';
    const STATUS_ERROR = 'error';

    const TYPE_RENTER   = 'smart_move_renter';
    const TYPE_MANAGER = 'smart_move_manager';

    const METHOD_PROPERTY_ADD    = 'method_property_create';
    const METHOD_APPLICATION_ADD = 'method_application_create';

    const METHOD_RENTER_NEW = 'method_renter_new';

    const METHOD_RETRIEVE_EXAM    = 'method_retrieve_exam';
    const METHOD_EVALUATE_EXAM    = 'method_revaluate_exam';
    const METHOD_GENERATE_REPORTS = 'method_generate_reports';
    const METHOD_GET_REPORTS      = 'method_get_reports';

    const METHOD_APPLICANT_ADD           = 'method_applicant_add';
    const METHOD_APPLICANT_STATUS        = 'method_applicant_status';
    const METHOD_APPLICANT_ACCEPT_STATUS = 'method_applicant_accept_status';

    /**
     * @param SmartMoveModelInterface $model
     *
     * @return $this
     */
    public function setModel(SmartMoveModelInterface $model = null);

    /**
     * Process API call
     *
     * @param string $method
     *
     * @return mixed
     */
    public function proccess($method = '');
}
