<?php

namespace Erp\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Erp\CoreBundle\Controller\BaseController;
use Erp\StripeBundle\Entity\BalanceHistory;
use Erp\StripeBundle\Entity\Transaction;
use Erp\UserBundle\Entity\Charge;
use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Form\Type\ChargeFormType;
use Erp\UserBundle\Form\Type\LandlordPayFormType;
use Erp\StripeBundle\Entity\PaymentTypeInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class LandlordController extends BaseController {

    /**
     * payLandlordAction Step 1 (select) in twig
     * 
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function landlordListAction(Request $request) {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();
        $items = $this->em->getRepository(User::class)->findBy(array('manager' => $user));
        $stripeUserManager = $this->get('erp_stripe.stripe.entity.user_manager');

        return $this->render('ErpUserBundle:Landlords:pay_landlord.html.twig', [
                    'user' => $user,
                    'items' => $items,
                    'stripeUserManager' => $stripeUserManager,
                    'modalTitle' => 'Pay to landlords'
        ]);
    }

    /**
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function payLandlordAction(Request $request) {
        //TODO: Add bank account landlords

        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();
        $landlordId = $request->request->has('landlordId') ? $request->get('landlordId') : $request->get('erp_user_landlords_pay_landlord')['landlordId'];
        $landlord = $this->em->getRepository('ErpUserBundle:User')->findOneBy(['id' => $landlordId]);

        if ($landlord instanceof User) {
            //Second step

            /** @var $user \Erp\UserBundle\Entity\User */
            $charge = new Charge();
            $form = $this->createForm(new LandlordPayFormType(), $charge, array('landlordId' => $landlordId));
            $form->handleRequest($request);

            /** @var $manager \Erp\UserBundle\Entity\User */
            $manager = $landlord->getManager();

            if ($manager->getId() == $user->getId() && $form->isValid()) {
                //Third (Final) step

                $landlordStripeAccountInstance = $landlord->getStripeAccount();
                $managerStripeCustomer = $manager->getStripeCustomer();

                //TODO Add cache layer (APC or Doctrine)
                $stripeUserManager = $this->get('erp_stripe.stripe.entity.user_manager');
                /** @var BankAccount $bankAccount */
                /** get manager bank account details in website */
                $managerBankAccount = $stripeUserManager->getBankAccount($user);
                $managerBankAccountId = ($managerBankAccount) ? $managerBankAccount->id : null;

                /** get manager stripe customer id in website */
                $stripeCustomerInfo = $stripeUserManager->getCustomerInfo($user);
                $managerCustomerId = ($stripeCustomerInfo) ? $stripeCustomerInfo['id'] : null;

                /** get landlord & manager other info in website */
                /* $managerEmail = $user->getEmail();
                  $landlordEmail = $landlord->getEmail(); */

                $landlordStripeAccount = $landlord->hasStripeAccount();

                if ($landlordStripeAccount) {
                    $rawLandlordStripeAccount = $stripeUserManager->getStripeAccountInfo($landlord);
                    $landlordStripeAccount = $rawLandlordStripeAccount['id'];

                    /** get previous transaction balance for landlord */
                    $rawStripeId = $landlord->getStripeAccount();
                    $stId = $rawStripeId->getId();
                    $getTransaction = $this->em->getRepository(Transaction::REPOSITORY)->getLastTransactionData($stId);
                    $oldBalance = $getTransaction['balance'];
                }

                /** if manager didn't connect own bank account in website */
                if (!$managerCustomerId or !$managerBankAccountId) {
                    $erMsg = 'Manager can not transfer payments. Because could not verify own bank account';
                    return $this->render('ErpUserBundle:Landlords:transferFailed.html.twig', [
                                'charge' => $charge,
                                'modalTitle' => $erMsg,
                                'user' => $user,
                                'landlord' => $landlord
                    ]);
                } else {
                    /** if landlord didn't have stripe connect account id in website */
                    if (!$landlordStripeAccount) {
                        $erMsg = 'Landlord can not accept payments. Because could not find stripe account id';
                        return $this->render('ErpUserBundle:Landlords:transferFailed.html.twig', array(
                                    'charge' => $charge,
                                    'modalTitle' => $erMsg,
                                    'user' => $user,
                                    'landlord' => $landlord
                        ));
                    } else {
                        /** Charge data set */
                        $charge
                                ->setManager($user)
                                ->setLandlord($landlord)
                        ;
                        $this->em->persist($charge);
                        $this->em->flush();

                        $charge = $this->em->getRepository(Charge::class)->find($charge->getId());

                        /** get transfer payment details from form fill data */
                        $rawData = $request->get('erp_user_landlords_pay_landlord');
                        $amount = $rawData['amount'];
                        $description = $request->get('description');
                        
                        $metadata = array(
                            'account' => $landlord->getStripeAccount()->getId(),
                            'internalType' => Transaction::INTERNAL_TYPE_PAY_LANDLORD,
                            'description' => $description
                        );
                        $chargeResponse = $this->get('erp.payment.service')
                                ->makeSinglePayment(null, $amount, $metadata, $landlord->getStripeAccount(), $managerCustomerId, $managerBankAccountId);

                        if (!$chargeResponse->isSuccess()) {
                            $erMsg = 'Transfer Failed due ' . $chargeResponse->getErrorMessage();
                            return $this->render('ErpUserBundle:Landlords:transferFailed.html.twig', array(
                                        'charge' => $charge,
                                        'modalTitle' => $erMsg,
                                        'user' => $user,
                                        'landlord' => $landlord
                            ));
                        }

                        $charge->setStatus(Charge::STATUS_PAID);
                        $this->em->flush();

                        /** Set stripe response for stripe transaction and balance history */
                        $rawcl = $chargeResponse->getContent();

                        $stAmount = $rawcl->amount;
                        $stBalance = $oldBalance + $stAmount;

                        /** Create Date Time object */
                        /** Create stripe transaction object and set data for store */
                        $rawDateTime = new \DateTime();
                        $transaction = new Transaction();
                        $transaction
                                ->setType($rawcl->object)
                                ->setAmount($stAmount)
                                ->setBalance($stBalance)
                                ->setBalanceHistory($balance)
                                ->setCurrency('usd')
                                ->setPaymentMethod('bank')
                                ->setPaymentMethodDescription($description)
                                ->setMetadata(json_encode($rawcl->metadata))
                                ->setStatus(\Erp\StripeBundle\Event\ChargeEvent::SUCCEEDED)
                                ->setInternalType(Transaction::INTERNAL_TYPE_PAY_LANDLORD)
                                ->setCreated($rawDateTime)
                                ->setCustomer($managerStripeCustomer)
                                ->setAccount($landlordStripeAccountInstance)
                                ->setStripeId($rawcl->id)
                        ;

                        $this->em->persist($transaction);
                        $this->em->flush();

                        /** Create balance history object */
                        $balance = new BalanceHistory();
                        /** Set balance history data and store  */
                        $balance
                                ->setBalance($stBalance)
                                ->setAmount($stAmount)
                                ->setTransaction($transaction)
                        ;
                        $this->em->persist($balance);
                        $this->em->flush();

                        $charge->datTimeMail = date('Y-m-d H:i:s', $rawcl->created);
                        $charge->txnId = $rawcl->transfer;

                        $from = $this->container->getParameter('contact_email');
                        $this->get('erp_user.mailer.processor')->sendTransferEmail($charge, $from);

                        $this->addFlash('alert_success', 'transfer successfully');
                        return $this->render('ErpUserBundle:Landlords:transferSent.html.twig', [
                                    'charge' => $charge,
                                    'modalTitle' => 'Transfer Successfully',
                                    'user' => $user,
                                    'landlord' => $landlord
                        ]);
                    }
                }
            }

            return $this->render('ErpUserBundle:Landlords:pay_landlord_step_2.html.twig', [
                        'user' => $user,
                        'landlord' => $landlord,
                        'form' => $form->createView(),
                        'modalTitle' => 'Pay to landlords'
            ]);
        } else {
            //back to landlords list to select
            $this->addFlash('alert_error', 'Choose any landlord');
            return $this->forward('ErpUserBundle:Landlord:LandlordList');
        }
    }
    
    /**
     * chargeAction Step 1 (select) in twig
     * 
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function indexAction(Request $request) {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();
        $landlords = $user->getLandlords();
        $tenants = $user->getTenants();

        return $this->render('ErpUserBundle:Landlords:index.html.twig', [
                    'user' => $user,
                    'landlords' => $landlords,
                    'tenants' => $tenants,
                    'modalTitle' => 'Charge clients'
        ]);
    }

    /**
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function chargeAction(Request $request) {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();
        $receiverIds = $request->get('receiverId');
        $receivers = $this->em->getRepository('ErpUserBundle:User')->findByArrayOfIds($receiverIds);

        if (count($receivers) > 0) {
            $emailsTo = array('success' => array(), 'error' => array());

            for ($i = 0; $i < count($receivers); $i++) {
                $ok = false;
                $receiver = $receivers[$i];

                /** @var $manager \Erp\UserBundle\Entity\User */
                $manager = $receiver->getRealManager();

                if ($manager->getId() == $user->getId()) {
                    //Second step

                    /** @var $user \Erp\UserBundle\Entity\User */
                    $charge = new Charge();
                    $form = $this->createForm(new ChargeFormType(), $charge);
                    $form->handleRequest($request);

                    if ($form->isValid()) {
                        //Third (Final) step

                        $charge->setManager($user);
                        $charge->setReceiver($receiver);

                        $this->em->persist($charge);
                        $this->em->flush();

                        if ($this->get('erp_user.mailer.processor')->sendChargeEmail($charge)) {
                            $charge->setStatus(Charge::STATUS_SENT);
                            $this->em->flush();

                            $ok = true;
                        }
                    }
                }

                if ($ok) {
                    $emailsTo['success'][] = $receiver->getEmail();
                } else {
                    $emailsTo['error'][] = $receiver->getEmail();
                }
            }

            if (count($emailsTo['success']) > 0) {
                return $this->render('ErpUserBundle:Landlords:chargeSent.html.twig', array(
                            'charge' => $charge,
                            'modalTitle' => 'Report',
                            'user' => $user,
                            'emailsTo' => $emailsTo
                ));
            }
            return $this->render('ErpUserBundle:Landlords:charge.html.twig', array(
                        'charge' => $charge,
                        'user' => $user,
                        'emailsTo' => $emailsTo['error'],
                        'receivers' => $receivers,
                        'form' => $form->createView()
            ));
        } else {
            //back to landlords/tenants list to select
            $this->addFlash('alert_error', 'Choose any landlord or tenant to charge');
            return $this->forward('ErpUserBundle:Landlord:index');
        }
    }

    /**
     * @param string $token
     * @return Response
     * @throws NotFoundHttpException
     */
    public function chooseChargeTypeAction($token) {
        /** @var Charge $charge */
        $charge = $this->em->getRepository(Charge::class)->find($token);

        if ($charge) {
            $template = 'ErpUserBundle:Landlords:choose_charge_type.html.twig';

            $params = array(
                'token' => $token,
                'charge' => $charge,
            );

            return $this->render($template, $params);
        } else {
            throw $this->createNotFoundException('Token ' . $token . ' not found');
        }
    }

    /**
     * @param Request $request
     * @param $type
     * @param $token
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function confirmChargeAction(Request $request, $type, $token) {
        /** @var Charge $charge */
        $charge = $this->em->getRepository(Charge::class)->find($token);
        /** @var PaymentTypeInterface $model */
        $model = $this->get('erp_stripe.registry.model_registry')->getModel($type);
        $form = $this->get('erp_stripe.registry.form_registry')->getForm($type);
        $form->setData($model);
        $form->handleRequest($request);

        $jsErrorMessage = $request->request->get('erp_stripe_credit_card')['js_error_message']; //message come from Stripe JS API

        $template = sprintf('ErpUserBundle:Landlords/Forms:%s.html.twig', $type);
        $params = array(
            'token' => $token,
            'form' => $form->createView(),
            'charge' => $charge
        );

        if ($jsErrorMessage) {
            $this->addFlash('alert_error', $jsErrorMessage);
            $this->get('erp.payment.service')->setChargeAsPending($charge);
            return $this->render($template, $params);
        }

        if ($form->isValid() && $charge->isPaid()) {
            $this->addFlash('alert_error', 'Already paid.');
        }

        if ($form->isValid()) {
            $managerStripeAccount = $charge->getManager()->getStripeAccount();

            if (!$managerStripeAccount) {
                $this->addFlash('alert_error', 'Manager can not accept payments.');
            }

            $manager = $this->get('erp.payment.service');

            $stripeAccountId = $managerStripeAccount->getAccountId();

            $receiverStripeCustomer = $charge->getReceiver()->getStripeCustomer();
            if (!$receiverStripeCustomer) {
                $response = $manager->createStripeCustomer($charge, $model->getSourceToken(), $stripeAccountId);
                if (!$response->isSuccess()) {
                    $this->addFlash(
                            'alert_error', $response->getErrorMessage()
                    );
                    return $this->render($template, $params);
                }
            }

            if ($charge->isRecurring()) {
                //TODO: add possibility for many subscriptions

                if (!$receiverStripeCustomer->getStripeSubscription()) {
                    $response = $manager->retrieveStripePlan($charge, $stripeAccountId);
                    if (!$response->isSuccess()) {
                        $this->addFlash(
                                'alert_error', $response->getErrorMessage()
                        );
                        return $this->render($template, $params);
                    }
                    
                    $response = $manager->createStripeSubscription($charge, $stripeAccountId);
                    if (!$response->isSuccess()) {
                        $this->addFlash(
                                'alert_error', $response->getErrorMessage()
                        );
                        return $this->render($template, $params);
                    }
                } else {
                    $response = $manager->updateStripeSubscription($charge);
                    if (!$response->isSuccess()) {
                        $this->addFlash(
                                'alert_error', $response->getErrorMessage()
                        );
                        return $this->render($template, $params);
                    }
                }
            } else {
                $metadata = array(
                    'account' => $stripeAccountId,
                    'internalType' => Transaction::INTERNAL_TYPE_CHARGE,
                    'description' => $charge->getDescription(),
                    'internalChargeId' => $charge->getId()
                );
                $response = $this->get('erp.payment.service')->makeSinglePayment($charge, $charge->getAmount(), $metadata, $managerStripeAccount);
            }
            if (!$response->isSuccess()) {
                $this->addFlash('alert_error', $response->getErrorMessage());
                return $this->render($template, $params);
            }

            $charge->setStatus(Charge::STATUS_PAID);

            $this->em->persist($charge);
            $this->em->flush();

            $template = 'ErpUserBundle:Landlords:chargeComplete.html.twig';
            $params = array('charge' => $charge);
        }

        return $this->render($template, $params);
    }
    
}
