<?php

namespace Erp\PaymentBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\PaymentBundle\Entity\StripeAccount;
use Erp\PaymentBundle\Entity\StripeDepositAccount;
use Erp\PaymentBundle\Entity\StripeCustomer;
use Erp\PaymentBundle\Form\Type\StripeCreditCardType;
use Erp\PaymentBundle\Plaid\Exception\ServiceException;
use Erp\PaymentBundle\Stripe\Model\CreditCard;
use Erp\UserBundle\Entity\User;
use Erp\StripeBundle\Form\Type\AccountVerificationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Erp\PropertyBundle\Entity\PropertySecurityDeposit;
use Stripe\Account;
use Stripe\Customer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StripeController extends BaseController {

    /**
     * 
     * @param Request $request
     * @return type
     */
    public function saveCreditCardAction(Request $request) {
        $form = $this->createForm(new StripeCreditCardType(), new CreditCard());
        $form->handleRequest($request);
        /** @var $user User */
        $user = $this->getUser();

        $template = 'ErpPaymentBundle:Stripe/Forms:cc.html.twig';
        $templateParams = [
            'user' => $user,
            'form' => $form->createView(),
            'errors' => null
        ];

        if ($form->isValid()) {
            $manager = $user->getTenantProperty()->getUser();
            $managerStripeAccountId = $manager->getStripeAccount()->getAccountId();

            $stripeToken = $form->getData()->getToken();
            $options = ['stripe_account' => $managerStripeAccountId];

            $stripeCustomer = $user->getStripeCustomer(StripeCustomer::CREDIT_CARD);
            $customerManager = $this->get('erp.payment.stripe.manager.customer_manager');

            if (!$stripeCustomer) {
                $params = array(
                    'email' => $user->getEmail(),
                    'source' => $stripeToken,
                );
                $response = $customerManager->create($params, $options);

                if (!$response->isSuccess()) {
                    $templateParams['errors'] = $response->getErrorMessage();
                    return $this->render($template, $templateParams);
                }
            } else {
                $response = $customerManager->retrieve($stripeCustomer->getCustomerId(), $options);

                if (!$response->isSuccess()) {
                    $templateParams['errors'] = $response->getErrorMessage();
                    return $this->render($template, $templateParams);
                }

                /** @var Customer $customer */
                $customer = $response->getContent();
                $response = $customerManager->update($customer, array('source' => $stripeToken), $options);

                if (!$response->isSuccess()) {
                    $templateParams['errors'] = $response->getErrorMessage();
                    return $this->render($template, $templateParams);
                }
            }

            return $this->redirectToRoute('erp_user_profile_dashboard');
        }

        return $this->render($template, $templateParams);
    }

    /**
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyBankAccountAction(Request $request) {
        $publicToken = $request->get('publicToken');
        $accountId = $request->get('accountId');
        
        /** @var Erp\PaymentBundle\Service\Service $manager */
        $manager = $this->get('erp.payment.service');

        try {
            $stripeBankAccountToken = $manager->createBankAccountToken($publicToken, $accountId);
        } catch (ServiceException $ex) {
            $this->addFlash('alert_error', $ex->getMessage());

            return $this->redirect($this->generateUrl('erp_user_dashboard_dashboard'));
        }
        
        // build the suitable variables to subsequently run the APIs
        $url = $this->generateUrl('erp_user_profile_dashboard');
        /** @var $user User */
        $user = $this->getUser();

        // identify the eventual Stripe Account to send to the Stripe API
        $options = null;
        if ($user->hasRole(User::ROLE_TENANT)) {
            if ($user->getTenantProperty() && ($user->getTenantProperty()->getUser()->getId() == $user->getId())) {
                $options = array(
                    'stripe_account' => $user->getTenantProperty()->getUser()->getStripeAccount()->getAccountId()
                );
            } else {
                return new JsonResponse('This tenant user has no properties.', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        // manage Stripe Customer
        list($response, $notExistingStripeCustomer) = $manager->manageStripeCustomer($user, $stripeBankAccountToken, $options);
        if (!$response->isSuccess()) {
            if ($notExistingStripeCustomer) {
                return new JsonResponse($response->getErrorMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            } else {
                return $this->returnRedirectResponseOnError($response);
            }
        }

        // manage Stripe Account, if the user is a manaager
        if ($user->hasRole(User::ROLE_MANAGER)) {
            $url = $this->generateUrl('erp_property_unit_buy');

            $response = $manager->manageStripeAccount(
                    $user,
                    $stripeBankAccountToken,
                    StripeAccount::DEFAULT_ACCOUNT_COUNTRY,
                    StripeAccount::DEFAULT_ACCOUNT_TYPE,
                    $options
            );
        }

        $this->addFlash('alert_ok', 'Bank account has been verified successfully');
        return $this->redirect($url);
    }

    /**
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyBankAccountDepositAction(Request $request) {
        $publicToken = $request->get('publicToken');
        $accountId = $request->get('accountId');
        $propertyId = $request->get('propertyId');
        
        $user = $this->getUser();
        $property = $this->em->getRepository('ErpPropertyBundle:Property')->getPropertyByUser($user, $propertyId);
        
        $propertySecurityDeposit = $property->getSecurityDeposit() ? $property->getSecurityDeposit() : new PropertySecurityDeposit();
        $property->setSecurityDeposit($propertySecurityDeposit);
        $this->em->persist($property);
        
        /** @var Erp\PaymentBundle\Service\Service $manager */
        $stripeManager = $this->get('erp.payment.service');

        try {
            $stripeBankAccountToken = $stripeManager->createBankAccountToken($publicToken, $accountId);
        } catch (ServiceException $ex) {
            return new JsonResponse($ex->getMessage(), $ex->getCode());
        }
        
        // build the suitable variables to subsequently run the APIs
        $options = null;
        
        // manage Stripe Customer
        list($response, $existingStripeCustomer) = $stripeManager->manageStripeCustomer($user, $stripeBankAccountToken, $options);
        if (!$response->isSuccess()) {
            return new JsonResponse($response->getErrorMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // manage the Stripe Account for the deposit
        $account = $this->get('erp_stripe.entity.api_manager')->callStripeApi(
                        '\Stripe\Account', array('id' => $property->getUser()->getStripeAccount()->getAccountId(), 'options' => null)
                )
                ->getContent()
        ;
        if ($user->hasRole(User::ROLE_MANAGER)) {
            $response = $stripeManager->manageStripeAccount(
                    $user,
                    $stripeBankAccountToken,
                    StripeDepositAccount::DEFAULT_ACCOUNT_COUNTRY,
                    StripeDepositAccount::DEFAULT_ACCOUNT_TYPE,
                    $options,
                    $request->getClientIp(),
                    $property
            );
            if (!$response->isSuccess()) {
                return new JsonResponse($response->getErrorMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $account = $response->getContent();
        }
        $stripeAccount = $property->getDepositAccount();
        $bankAccount = $stripeManager->updateBankAccount($stripeAccount, $account, $stripeBankAccountToken);

        return new JsonResponse(array(
            'response' => $response,
            'bankAccount' => $bankAccount,
        ));
    }

    /**
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyAccountAction(Request $request) {
        //TODO Need to verify account if I change BA?
        /** @var User $user */
        $user = $this->getUser();
        $stripeAccount = $user->getStripeAccount();

        $form = $this->createForm(new AccountVerificationType(), $stripeAccount, ['validation_groups' => 'US']);
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            /** @var Erp\PaymentBundle\Service\Service $stripeManager */
            $stripeManager = $this->get('erp.payment.service');
            
            $response = $stripeManager->verifyAccount($stripeAccount, $request->getClientIp());
            
            if (!$response->isSuccess()) {
                return new JsonResponse([
                    'success' => false,
                    'error' => $response->getErrorMessage(),
                ]);
            }

            /** @var Account $content */
            $content = $response->getContent();
            if ($fieldsNeeded = $content->verification->fields_needed) {
                //TODO Handle Stripe required verification fields
                return new JsonResponse(array(
                    'success' => false,
                    'fields_needed' => $fieldsNeeded,
                ));
            }

            $this->em->persist($stripeAccount);
            $this->em->flush();

            if ($user->hasRole(User::ROLE_MANAGER)) {
                $url = $this->generateUrl('erp_property_unit_buy');
            } else {
                $url = $this->generateUrl('erp_user_profile_dashboard');
            }
            
            return new JsonResponse(array(
                'redirect' => $url,
            ));
        }
        //TODO Prepare backend errors for frontend
        return $this->render('ErpStripeBundle:Widget:verification_ba.html.twig', [
                    'form' => $form->createView(),
                    'modalTitle' => 'Continue verification',
        ]);
    }
    
    /**
     * Payment Preferences widget
     *
     * @Security("is_granted('ROLE_TENANT') or is_granted('ROLE_MANAGER')")
     * 
     * @param Request $request
     * @return Response
     */
    public function paymentsPreferencesAction(Request $request) {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();
        
        if ($request->get('to')) {
            $user->setPrimaryPaymentType($to);
        }

        return $this->render('ErpPaymentBundle:Stripe/Widgets:payments-preferences.html.twig', array(
                    'customerCC' => $user->getStripeCustomer(StripeCustomer::CREDIT_CARD),
                    'customerBA' => $user->getStripeCustomer(StripeCustomer::BANK_ACCOUNT),
                    'user' => $user,
                    'ccTransactionFeePercent' => $this->get('erp.core.fee.service')->getCcTransactionFee(),
                    'achTransactionFeeAmount' => $this->get('erp.core.fee.service')->getAchTransactionFee(),
                    'checkPaymentAmount' => $this->get('erp.core.fee.service')->getCheckPaymentFee(),
        ));
    }

    /**
     * 
     * @param mixed $response
     * @return RedirectResponse
     */
    private function returnRedirectResponseOnError($response) {
        $this->addFlash('alert_error', $response->getErrorMessage());
        return $this->redirect($this->generateUrl('erp_user_dashboard_dashboard'));
    }

}
