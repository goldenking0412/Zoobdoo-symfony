<?php

namespace Erp\StripeBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Erp\CoreBundle\Controller\BaseController;
use Erp\StripeBundle\Entity\Transaction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RefundController extends BaseController {

    /**
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     */
    public function confirmAction(Request $request, $transactionId) {
        return $this->render('ErpStripeBundle:Refund:confirm.html.twig', array(
                    'pathApply' => $this->generateUrl('erp_stripe_transaction_apply', array('transactionId' => $transactionId))
        ));
    }

    /**
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     */
    public function applyAction(Request $request, $transactionId) {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();

        /** @var $user \Erp\StripeBundle\Entity\Transaction */
        $transaction = $this->em->getRepository(Transaction::REPOSITORY)->find($transactionId);

        $classMessage = 'alert-danger';
        
        if ($transaction) {
            $stripeCustomer = $user->getStripeCustomer();
            if (!$stripeCustomer) {
                $message = 'Your Stripe Account is invalid';
                $code = Response::HTTP_NOT_FOUND;
            } else {
                $manager = $this->get('erp.payment.service');
                $response = $manager->refund($stripeCustomer, $transaction);
                
                if ($response->isSuccess()) {
                    $classMessage = 'alert-success';
                    $message = 'You have successfully refunded the transaction';
                    $code = Response::HTTP_OK;
                    
                    $transaction->setRefunded(true);
                    $this->em->persist($transaction);
                    $this->em->flush();
                } else {
                    $message = 'Something went wrong during Stripe connection.
                            Reason: ' . $response->getErrorReasonCode() . '. Message: ' . $response->getErrorMessage() . '.
                            Please, contact us for further details.'
                    ;
                    $code = ($response->getErrorResponseCode() == 0)
                            ? Response::HTTP_INTERNAL_SERVER_ERROR
                            : $response->getErrorResponseCode();
                }
            }
        } else {
            $error = $this->createNotFoundException();

            $message = $error->getMessage();
            $code = $error->getCode();
        }

        return $this->render('ErpStripeBundle:Refund:apply.html.twig', array(
                    'message' => $message,
                    'class' => $classMessage
        ), new Response($message, $code));
    }

}
