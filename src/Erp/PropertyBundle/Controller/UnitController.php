<?php

namespace Erp\PropertyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Erp\PropertyBundle\Entity\Property;
use Erp\PropertyBundle\Entity\PropertySettings;
use Erp\PropertyBundle\Form\Type\UnitType;
use Erp\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class UnitController extends Controller {

    /**
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     * 
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws AccessDeniedException
     */
    public function buyAction(Request $request) {
        $form = $this->createForm(new UnitType());
        /** @var User $user */
        $user = $this->getUser();
        
        $template = 'ErpPropertyBundle:Unit:form.html.twig';

        if ($user->hasStripeCustomers()) {
            $settings = $this->getParameter('erp_buy_unit_settings');

            $existingUnitQuantity = $user->getProperties()->count();

            $unitPriceCalculator = $this->get('erp_property.calculator.unit_price_calculator');
            $amount = $unitPriceCalculator->calculate($settings, $existingUnitQuantity);
            
            $templateParams = array(
                'user' => $user,
                'form' => $form->createView(),
                'errors' => null,
                'current_year_price' => $amount,
                'total_price' => $amount,
                'existing_unit_count' => $existingUnitQuantity,
                'settings' => $settings,
            );

            $form->handleRequest($request);

            if (!$form->isValid()) {
                return $this->render($template, $templateParams);
            }

            $quantity = $form->getData()->getQuantity();
            $newAmount = $unitPriceCalculator->calculate($settings, $quantity + $existingUnitQuantity);
            
            $response = $this->get('erp.payment.service')->buyUnit($user, $amount, $newAmount);
            
            if (!$response->isSuccess()) {
                $templateParams['errors'] = $response->getErrorMessage();
                return $this->render($template, $templateParams);
            }

            $this->addProperties($user, $quantity);

            return $this->redirectToRoute('erp_property_listings_all');
        } else {
            /* $templateParams = array(
                'user' => $user,
                'form' => $form->createView(),
                'errors' => 'Please, add bank account.'
            );
            
            return $this->render($template, $templateParams); */
            throw $this->createAccessDeniedException('Please, add bank account.');
        }
    }

    /**
     * 
     * @param User $user
     * @param type $quantity
     */
    private function addProperties(User $user, $quantity) {
        $em = $this->getDoctrine()->getManagerForClass(Property::class);

        $prototype = new Property();
        for ($i = 1; $i <= $quantity; $i++) {
            $property = clone $prototype;

            if ($user->hasRole(User::ROLE_MANAGER)) {
                $property->setUser($user);
            } elseif ($user->hasRole(User::ROLE_LANDLORD)) {
                $property->setLandlordUser($user);
            }

            $property->setSettings(new PropertySettings());

            $em->persist($property);
        }

        $em->flush();
    }

}
