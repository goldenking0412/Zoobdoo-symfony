<?php

namespace Erp\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CityController extends BaseController
{
    /**
     * Get list cities by state code
     *
     * @param string $stateCode
     *
     * @return JsonResponse
     */
    public function getCitiesByStateCodeAction($stateCode = null)
    {
        $this->get('erp.core.location')->setCities($stateCode);
        $cities = $this->get('erp.core.location')->getCities();

        $result = [];
        foreach ($cities as $id => $name) {
            $result[] = [
                'id' => $id,
                'name' => $name
            ];
        }

        return new JsonResponse($result);
    }
}
