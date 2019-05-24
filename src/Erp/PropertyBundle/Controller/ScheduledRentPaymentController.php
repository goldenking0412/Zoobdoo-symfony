<?php

namespace Erp\PropertyBundle\Controller;

use Erp\PaymentBundle\Entity\StripeCustomer;
use Erp\PropertyBundle\Entity\ScheduledRentPayment;
use Erp\PropertyBundle\Entity\PropertySecurityDeposit;
use Erp\PropertyBundle\Form\Type\StopAutoWithdrawalFormType;
use Erp\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Erp\PropertyBundle\Form\Type\ScheduledRentPaymentType;

class ScheduledRentPaymentController extends Controller {

    /**
     * @Security("is_granted('ROLE_TENANT')")
     * 
     * @param Request $request
     * @return Response
     */
    public function payRentAction(Request $request) {
        /** @var User $user */
        $user = $this->getUser();
        $twigTemplate = 'ErpPaymentBundle:Stripe\Widgets:rental-payment.html.twig';
        
        $property = $user->getTenantProperty();
        
        // if not property exists, there is a problem within the database
        if (!($property)) {
            return $this->render($twigTemplate, array(
                'exception' => $this->createNotFoundException('There is a misalignment within the database: no property is available.')
            ));
        }
        
        // show if all the payments have been made in-time
        $securityDeposit = $property->getSecurityDeposit();
        if (!($user->isDebtor()) && ($securityDeposit->getStatus() != PropertySecurityDeposit::STATUS_DEPOSIT_UNPAID)) {
            return $this->render($twigTemplate, array(
                'info' => 'You are up to date with payments. Thank you.'
            ));
        }
        
        // show or process the form
        $manager = $property->getUser();

        $managerStripeAccount = ($manager) ? $manager->getStripeAccount() : null;
        $tenantStripeCustomer = $user->getStripeCustomer();

        $entity = new ScheduledRentPayment();
        $entity->setProperty($property);
        $entity->setUser($user);

        $form = $this->createForm(ScheduledRentPaymentType::class, $entity);
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // if the ScheduledRentPayment is a deposit_payment, then the manager stripe account
                // is taken from security deposit of property, if not already existing
                $managerStripeAccount = ($managerStripeAccount)
                        ?
                        : ($entity->isDepositPayment() && !($securityDeposit->getSendToMainAccount())) ? $property->getDepositAccount() : null
                ;

                if (!$managerStripeAccount || !$tenantStripeCustomer) {
                    $this->addFlash(
                            'alert_error', 'Please, add your payment info or contact your manager.'
                    );
                    return $this->redirectToRoute('erp_user_profile_dashboard');
                }
                
                $metadata = array(
                    'account' => $managerStripeAccount->getAccountId(),
                    'internalType' => $entity->getCategory()
                );

                $response = $this->get('erp.payment.service')->makeSinglePayment($entity, $entity->getAmount(), $metadata, $managerStripeAccount);

                // now, saving the entities
                if ($response->isSuccess()) {
                    // saving the ScheduledRentPayment entity if success only
                    $entityType = ($form->getData()->getType() == 1) ? ScheduledRentPayment::TYPE_RECURRING : ScheduledRentPayment::TYPE_SINGLE;

                    $entity
                            ->setNextPaymentAt($entity->getStartPaymentAt()->add(new \DateInterval('P1M')))
                            ->setAccount($managerStripeAccount)
                            ->setCustomer($tenantStripeCustomer)
                            ->setType($entityType);

                    $em = $this->getDoctrine()->getManagerForClass(ScheduledRentPayment::class);
                    $em->persist($entity);

                    $this->addFlash(
                            'alert_ok', 'Success'
                    );
                } else {
                    $this->addFlash('alert_error', json_encode($response->getErrorMessage()));
                }

                // updating and saving the $securityDepositStatus
                $securityDepositStatus = $this->getSecurityDepositStatusOnPayRent($entity, $response);
                if ($securityDepositStatus) {
                    $securityDeposit->setStatus($securityDepositStatus);
                    $em = $this->getDoctrine()->getManagerForClass(PropertySecurityDeposit::class);
                    $em->persist($securityDeposit);
                }

                $em->flush();

                return $this->redirectToRoute('erp_user_profile_dashboard');
            } else {
                $this->addFlash(
                        'alert_error', $form->getErrors(true)[0]->getMessage()
                );
                return $this->redirectToRoute('erp_user_profile_dashboard');
            }
        }
        
        return $this->render($twigTemplate, array(
                    'form' => $form->createView(),
                    'user' => $user,
                    'manager' => $manager,
        ));
    }

    /**
     * 
     * @param User $user
     * @param Request $request
     * @return Response
     * @throws AccessDeniedException
     */
    public function stopAutoWithdrawalAction(User $user, Request $request) {
        if ($user->hasNoStripeCustomers()) {
            throw $this->createNotFoundException();
        }

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($currentUser->hasTenant($user)) {
            throw $this->createAccessDeniedException();
        }

        $entity = new ScheduledRentPayment();
        $form = $this->createForm(new StopAutoWithdrawalFormType(), $entity, ['validation_groups' => 'StopAuthWithdrawal']);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $stripeCustomer = $user->getStripeCustomer();
            
            $endAt = $entity->getEndAt();
            $scheduledRentPayments = $stripeCustomer->getScheduledRentPayments();
            /** @var ScheduledRentPayment $scheduledRentPayment */
            foreach ($scheduledRentPayments as $scheduledRentPayment) {
                $scheduledRentPayment->setEndAt($endAt);
            }

            $em = $this->getDoctrine()->getManagerForClass(StripeCustomer::class);
            $em->persist($stripeCustomer);
            $em->flush();

            $this->addFlash(
                    'alert_ok', 'Success'
            );
        }

        return $this->redirect($request->headers->get('referer'));
    }
    
    /**
     * 
     * @param ScheduledRentPayment $payment
     * @param type $response
     * @return string|null
     */
    protected function getSecurityDepositStatusOnPayRent(ScheduledRentPayment $payment, $response) {
        $securityDepositStatus = null;
        
        if ($payment->isDepositPayment()) {
            $securityDepositStatus = ($response->isSuccess())
                    ? PropertySecurityDeposit::STATUS_DEPOSIT_PAID
                    : PropertySecurityDeposit::STATUS_DEPOSIT_UNPAID
            ;
        }
        
        return $securityDepositStatus;
    }

}
