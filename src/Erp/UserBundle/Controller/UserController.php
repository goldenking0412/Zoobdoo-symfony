<?php

namespace Erp\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Form\Type\UserLateRentPaymentType;
use Erp\CoreBundle\Controller\BaseController;

class UserController extends BaseController {

    public function allowRentPaymentAction(User $user, Request $request) {
        $currentUser = $this->getUser();
        if ($currentUser->hasTenant($user)) {
            return $this->createNotFoundException();
        }

        $form = $this->createForm(new UserLateRentPaymentType(), $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->em->persist($user);
            $this->em->flush();

            return new JsonResponse([
                'success' => true,
            ]);
        }

        return new JsonResponse([
            'success' => false,
            'error' => 'An occurred error.',
        ]);
    }

}
