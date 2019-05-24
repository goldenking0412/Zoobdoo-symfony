<?php

namespace Erp\UserBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Erp\UserBundle\Entity\User;

/**
 * Class SecurityController
 *
 * @package Erp\UserBundle\Controller
 */
class SecurityController extends BaseController {

    /**
     * Login page
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function loginAction(Request $request) {
        if ($this->getUser()) {
            $response = $this->redirectToRoute('erp_user_profile_dashboard');
        } else {
            // $response = parent::loginAction($request);
            $response = $this->redirectToRoute('erp_site_homepage');
        }

        return $response;
    }

    /**
     * Check login action
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function checkAction() {
        $request = $this->get('request');
        if ($request->isXmlHttpRequest()) {
            $response = $this->checkUserOnXHR($request);
        } else {
            $response = parent::checkAction();
        }
        
        return $response;
    }

    /**
     * 
     * @param Request $request
     * @return \Erp\UserBundle\Controller\RedirectResponse
     */
    protected function checkUserOnXHR(Request $request) {
        $found = false;
        $code = Response::HTTP_UNAUTHORIZED;
        
        $username = $request->request->get('_username');
        $password = $request->request->get('_password');

        if(is_null($username) || is_null($password)) {
            $response = 'Please verify all your inputs.';
        }

        // check if an user exists and the provided password is valid
        $user = $this->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);
        if ($user) {
            $encoder = $this->get('security.encoder_factory')->getEncoder($user);
            $salt = $user->getSalt();
            
            if ($encoder->isPasswordValid($user->getPassword(), $password, $salt)) {
                $found = true;
            } else {
                $response = 'Password not valid.';
            }
        } else {
            $response = 'User does not exist.';
        }
        
        // if user has been found
        if ($found) {
            $code = Response::HTTP_OK;
            $url = 'erp_user_profile_dashboard';
            
            if ($user->hasRole(User::ROLE_ADMIN) || $user->hasRole(User::ROLE_SUPER_ADMIN)) {
                $url = 'sonata_admin_dashboard';
            } elseif ($user->hasRole(User::ROLE_TENANT) || $user->hasRole(User::ROLE_MANAGER)) {
                if ($user->hasRole(User::ROLE_MANAGER)) {
                    $url = 'erp_user_dashboard_dashboard';
                }

                if (!$user->getIsTermOfUse()) {
                    $url = 'erp_user_term_of_use';
                }
            }
            $response = array('redirect' => $this->get('router')->generate($url));
            
            // manually logging in the user
            $this->get('fos_user.security.login_manager')->logInUser('main', $user);
        }

        return new JsonResponse($response, $code);
    }

}
