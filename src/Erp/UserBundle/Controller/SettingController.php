<?php

namespace Erp\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Erp\CoreBundle\Controller\BaseController;
use \Exception;

/**
 * Class SettingController
 *
 * @package Erp\UserBundle\Controller
 */
class SettingController extends BaseController {

    /**
     * Page settings
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function changeUserSettingsAction(Request $request) {
        try {
            $user = $this->getUser();
            if (!$user->getSecondEmail()) {
                $user->setSecondEmail($user->getEmail());
            }

            $secondEmail = $request->get('second_email');
            $settings = $request->get('settings');

            $user->setSettings($settings);
            $user->setSecondEmail($secondEmail);
            
            $this->em->flush();
            
            $code = Response::HTTP_OK;
            $message = array(
                'result' => 'ok',
                'second_email' => $user->getSecondEmail(),
                'settings' => $settings
            );
        } catch (Exception $ex) {
            $code = $ex->getCode();
            $message = $ex->getMessage();
        }

        return new JsonResponse($message, $code);
    }

}
