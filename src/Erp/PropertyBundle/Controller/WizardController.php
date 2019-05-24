<?php

namespace Erp\PropertyBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\CoreBundle\Entity\Document;
use Erp\CoreBundle\Entity\Image;
use Erp\PropertyBundle\Entity\Property;
use Erp\PropertyBundle\Entity\PropertySettings;
use Erp\PropertyBundle\Entity\PropertySecurityDeposit;
use Erp\PaymentBundle\Entity\StripeDepositAccount;
use Erp\PropertyBundle\Entity\ScheduledRentPayment;
use Erp\PropertyBundle\Form\Type\EditImagePropertyFormType;
use Erp\PropertyBundle\Form\Type\EditPropertyFormType;
use Erp\PropertyBundle\Form\Type\PropertySettingsType;
use Erp\PropertyBundle\Form\Type\PropertySecurityDepositType;
use Erp\PropertyBundle\Form\Type\StopAutoWithdrawalFormType;
use Erp\PropertyBundle\Form\Type\InviteTenantWizardCollectionFormType;
use Erp\UserBundle\Form\Type\LandlordFormType;
use Erp\UserBundle\Form\Type\ManagerFormType;
use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Entity\InvitedUser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;
use Erp\PaymentBundle\Plaid\Exception\ServiceException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class WizardController extends BaseController {

    /**
     * Wizard Property page
     * 
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     *
     * @param Request $request
     * @param int|null $propertyId
     * @return RedirectResponse|Response|NotFoundHttpException
     */
    public function wizardAction(Request $request, $propertyId) {
        /** STEP 1 * */
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();

        if ($propertyId) {
            $property = $this->em->getRepository('ErpPropertyBundle:Property')->getPropertyByUser($user, $propertyId);

            if (!$property) {
                throw $this->createNotFoundException();
            }
        } else {
            if ($user->getPropertyCounter() > 0 || $user->getIsPropertyCounterFree()) {
                $property = new Property();

                if ($user->hasRole(User::ROLE_MANAGER)) {
                    $property->setUser($user);
                } elseif ($user->hasRole(User::ROLE_LANDLORD)) {
                    $property->setLandlordUser($user);
                }
            } else {
                throw $this->createNotFoundException();
            }
        }
        $xhr = $request->get('xhr', false);
        $pageNumber = $request->get('page', 1);

        $action = $this->generateUrl('erp_property_listings_wizard_add');
        if ($property->getId()) {
            $action = $this->generateUrl('erp_property_listings_wizard_edit', ['propertyId' => $property->getId()]);
        }
        $formOptions = ['action' => $action, 'method' => 'POST'];
        $form = $this->createForm(new EditPropertyFormType($this->container), $property, $formOptions);
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                /** @var $property \Erp\PropertyBundle\Entity\Property */
                $property = $form->getData();

                if (!$user->getIsPropertyCounterFree() && !$user->isReadOnlyUser() && !$propertyId) {
                    $this->em->persist($user->setPropertyCounter($user->getPropertyCounter() - 1));
                }

                $this->em->persist($property);
                $this->em->flush();


                if ($xhr) {
                    $this->addFlash('alert_ok', 'Property was saved successfully.');
                    return $this->redirect($this->generateUrl('erp_property_listings_all'));
                } else {
                    return $this->redirectToRoute('erp_property_listings_wizard_edit_images', ['propertyId' => $property->getId()]);
                }
            }
        }
        if ($request->isXmlHttpRequest()) {
            return $this->render('ErpPropertyBundle:Wizard/blocks:property-details.html.twig', [
                        'form' => $form->createView(),
                        'user' => $user,
                        'property' => $property,
                        'page' => $pageNumber,
                        'xhr' => 1,
                        'modalTitle' => 'Property Details',
                        'buttonLabel' => 'Submit'
            ]);
        } else {
            return $this->render('ErpPropertyBundle:Wizard:property-details.html.twig', array(
                        'user' => $user,
                        'form' => $form->createView(),
                        'property' => $property,
                        'page' => $pageNumber,
                        'buttonLabel' => 'Next',
                        'xhr' => 0,
            ));
        }
    }

    /**
     * Manage listing documents action
     *
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     * 
     * @param Request $request
     * @param int $propertyId
     * @return Response
     */
    public function wizardEditImagesAction(Request $request, $propertyId) {
        /** upload images * */
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();

        /** @var Property $property */
        $property = $this->em->getRepository('ErpPropertyBundle:Property')->getPropertyByUser($user, $propertyId);

        if (!$property) {
            throw $this->createNotFoundException();
        }

        $pageNumber = $request->get('page', 1);

        $action = $this->generateUrl(
                'erp_property_listings_wizard_edit_images', ['propertyId' => $property->getId(), 'page' => $pageNumber]
        );

        $formOptions = ['action' => $action, 'method' => 'POST'];
        $form = $this->createForm(new EditImagePropertyFormType($this->container), $property, $formOptions);

        if ($request->getMethod() == 'POST') {
            $preValidate = $this->preValidateFiles(
                    $request, $property, $form->getName(), ['images', 'image']
            );

            $request = $preValidate['request'];
            $errors = $preValidate['errors'];

            $form->handleRequest($request);
//            if ($form->isValid()) {
//                $this->em->persist($property);
//                $this->em->flush();
//            } else {
//                $errors = true;
//            }

            if ($errors) {
                $text = str_replace(
                        ['{maxSize}', '{sizeIn}'], [Image::$maxSize / 1024 / 1024, Document::SIZE_IN_MB], Image::$commonMessage
                );

                $this->addFlash('alert_error', $text);
            }

            $this->em->persist($property);
            $this->em->flush();

            return $this->redirectToRoute(
                            'erp_property_listings_wizard_payment_settings', ['propertyId' => $property->getId()]
            );
        }

        return $this->render(
                        'ErpPropertyBundle:Wizard:property-images.html.twig', ['user' => $user, 'form' => $form->createView(), 'property' => $property, 'page' => $pageNumber]
        );
    }

    /**
     * Wizard Settings page
     * 
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     *
     * @param Request $request
     * @param int|null $propertyId
     * @return RedirectResponse|Response|NotFoundHttpException
     */
    public function wizardPaymentSettingsAction(Request $request, $propertyId) {
        /** step 3 * */
        /** @var $user User */
        $user = $this->getUser();
        $xhr = $request->get('xhr', false);
        $em = $this->getDoctrine()->getManagerForClass(Property::class);
        /** @var Property $property */
        $property = $em->getRepository(Property::class)->getPropertyByUser($user, $propertyId);

        if (!$property) {
            throw $this->createNotFoundException();
        }

        $propertySettings = $property->getSettings() ?: new PropertySettings();
        $property->setSettings($propertySettings);


        $action = $this->generateUrl('erp_property_listings_wizard_payment_settings', ['propertyId' => $property->getId()]);
        $formOptions = ['action' => $action, 'method' => 'POST'];

        $form = $this->createForm(new PropertySettingsType($this->container), $propertySettings, $formOptions);
        $form->handleRequest($request);


        $scheduledRentPayment = new ScheduledRentPayment();
        $stopAutoWithdrawalForm = $this->createForm(new StopAutoWithdrawalFormType(), $scheduledRentPayment);

        if ($request->getMethod() == 'POST') {
            if ($form->isValid()) {
                $data = $form->getData();
                $em->persist($property);
                $em->flush();
                if ($xhr) {
                    $this->addFlash('alert_ok', 'Success');
                    return $this->redirect($this->generateUrl('erp_property_listings_all'));
                } else {
                    $stringRoute = $user->hasRole(User::ROLE_LANDLORD) ? 'erp_property_listings_wizard_manager' : 'erp_property_listings_wizard_landlord';
                    
                    return $this->redirectToRoute($stringRoute, ['propertyId' => $property->getId()]);
                }
            }
        }
        if ($request->isXmlHttpRequest()) {
            return $this->render('ErpPropertyBundle:Wizard/blocks:setup-lease.html.twig', [
                        'user' => $user,
                        'form' => $form->createView(),
                        'autoWithdrawalForm' => $stopAutoWithdrawalForm->createView(),
                        'property' => $property,
                        'modalTitle' => 'Setup Lease',
                        'buttonLabel' => 'Submit',
                        'xhr' => 1,
            ]);
        } else {
            return $this->render('ErpPropertyBundle:Wizard:setup-lease.html.twig', [
                        'user' => $user,
                        'form' => $form->createView(),
                        'autoWithdrawalForm' => $stopAutoWithdrawalForm->createView(),
                        'property' => $property,
                        'buttonLabel' => 'Next',
                        'xhr' => 0,
            ]);
        }
    }
    
    /**
     * Manage manager of property
     * 
     * @Security("is_granted('ROLE_LANDLORD')")
     *
     * @param Request $request
     * @param int $propertyId
     * @return Response
     */
    public function wizardManagerAction(Request $request, $propertyId) {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();

        $property = $this->em->getRepository('ErpPropertyBundle:Property')->getPropertyByUser($user, $propertyId);

        $propertyFee = $this->get('erp.core.fee.service')->getPropertyFee();

        $action = $this->generateUrl(
                'erp_property_listings_wizard_manager', ['propertyId' => $propertyId]
        );
        $formOptions = ['action' => $action, 'method' => 'POST'];
        $manager = new User();
        $form = $this->createForm(new ManagerFormType(), $manager, $formOptions);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $xhr = $request->get('xhr', false);
                
                $existingManager = $this->em->getRepository(User::REPOSITORY)
                        ->findOneByEmail($form->getData()->getEmail())
                ;
                
                if ($existingManager) {
                    $existingManager
                            ->addLandlord($user)
                            ->setPropertyCounter($existingManager->getPropertyCounter() + 1)
                    ;
                    
                    // $user->setManager($existingManager);
                    
                    $property->setUser($manager);
                    
                    $this->em->flush();
                } else {
                    $manager
                            ->addLandlord($user)
                            ->setUsername($manager->getEmail())
                            ->setPropertyCounter(User::DEFAULT_PROPERTY_COUNTER)
                            ->setApplicationFormCounter(User::DEFAULT_APPLICATION_FORM_COUNTER)
                            ->setContractFormCounter(User::DEFAULT_CONTRACT_FORM_COUNTER)
                            ->addRole(User::ROLE_MANAGER)
                    ;
                    $property->setUser($manager);
                    
                    $this->em->persist($manager);
                    $this->em->persist($property);
                    $this->em->flush();
                    
                    $this->sendInviteManagerEmail($user, $manager);
                }
                
                if ($xhr) {
                    $this->addFlash('alert_ok', 'Manager was saved successfully.');
                    return $this->redirect($this->generateUrl('erp_property_listings_all'));
                } else {
                    return $this->redirectToRoute('erp_property_listings_wizard_invited_tenant', ['propertyId' => $property->getId()]);
                }
            } else {
                return new JsonResponse(array(
                    'success' => false,
                    'errors' => $form->getErrors(),
                ));
            }
        }
        
        $allManagers = $this->em->getRepository(User::REPOSITORY)
                ->findByRole(User::ROLE_MANAGER)
        ;
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('ErpPropertyBundle:Wizard/blocks:manager.html.twig', [
                        'user' => $user,
                        'propertyFee' => $propertyFee,
                        'property' => $property,
                        'form' => $form->createView(),
                        'xhr' => true,
                        'propertyStatusRented' => Property::STATUS_RENTED,
                        'modalTitle' => 'Setup Landlord',
                        'buttonLabel' => 'Submit',
                        'allManagers' => $allManagers
            ]);
        } else {
            return $this->render('ErpPropertyBundle:Wizard:manager.html.twig', array(
                        'user' => $user,
                        'propertyFee' => $propertyFee,
                        'property' => $property,
                        'form' => $form->createView(),
                        'xhr' => false,
                        'propertyStatusRented' => Property::STATUS_RENTED,
                        'buttonLabel' => 'Next',
                        'allManagers' => $allManagers
            ));
        }
    }

    /**
     * Manage landlord of property
     * 
     * @Security("is_granted('ROLE_MANAGER')")
     *
     * @param Request $request
     * @param int $propertyId
     * @return Response
     */
    public function wizardLandlordAction(Request $request, $propertyId) {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();

        $property = $this->em->getRepository('ErpPropertyBundle:Property')->getPropertyByUser($user, $propertyId);

        $propertyFee = $this->get('erp.core.fee.service')->getPropertyFee();

        $action = $this->generateUrl(
                'erp_property_listings_wizard_landlord', ['propertyId' => $propertyId]
        );
        $formOptions = ['action' => $action, 'method' => 'POST'];
        $landlord = new User();
        $form = $this->createForm(new LandlordFormType(), $landlord, $formOptions);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $xhr = $request->get('xhr', false);
                
                $existingLandlord = $this->em->getRepository(User::REPOSITORY)
                        ->findOneByEmail($form->getData()->getEmail())
                ;
                
                if ($existingLandlord) {
                    $existingLandlord
                            ->setManager($user)
                            ->setPropertyCounter($existingLandlord->getPropertyCounter() + 1)
                    ;
                    
                    $property->setLandlordUser($existingLandlord);
                    
                    $this->em->flush();
                } else {
                    $landlord
                            ->setManager($user)
                            ->setUsername($landlord->getEmail())
                            ->setPassword('temporary')
                            ->setPropertyCounter(User::DEFAULT_PROPERTY_COUNTER)
                            ->setApplicationFormCounter(User::DEFAULT_APPLICATION_FORM_COUNTER)
                            ->setContractFormCounter(User::DEFAULT_CONTRACT_FORM_COUNTER)
                            ->addRole(User::ROLE_LANDLORD)
                    ;
                    $property->setLandlordUser($landlord);
                    
                    $this->em->persist($landlord);
                    $this->em->persist($property);
                    $this->em->flush();
                    
                    $this->sendInviteLandlordEmail($user, $landlord);
                }
                
                if ($xhr) {
                    $this->addFlash('alert_ok', 'Landlord was saved successfully.');
                    return $this->redirect($this->generateUrl('erp_property_listings_all'));
                } else {
                    return $this->redirectToRoute('erp_property_listings_wizard_invited_tenant', ['propertyId' => $property->getId()]);
                }
            } else {
                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(array(
                        'success' => false,
                        'errors' => (string) $form->getErrors(true),
                    ));
                } else {
                    $this->addFlash('alert_error', (string) $form->getErrors(true));
                }
            }
        }
        
        $allLandlords = $this->em->getRepository(User::REPOSITORY)->findByRole(User::ROLE_LANDLORD);
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('ErpPropertyBundle:Wizard/blocks:landlord.html.twig', [
                        'user' => $user,
                        'propertyFee' => $propertyFee,
                        'property' => $property,
                        'form' => $form->createView(),
                        'xhr' => true,
                        'propertyStatusRented' => Property::STATUS_RENTED,
                        'modalTitle' => 'Setup Landlord',
                        'buttonLabel' => 'Submit',
                        'allLandlords' => $allLandlords
            ]);
        } else {
            return $this->render('ErpPropertyBundle:Wizard:landlord.html.twig', array(
                        'user' => $user,
                        'propertyFee' => $propertyFee,
                        'property' => $property,
                        'form' => $form->createView(),
                        'xhr' => false,
                        'propertyStatusRented' => Property::STATUS_RENTED,
                        'buttonLabel' => 'Next',
                        'allLandlords' => $allLandlords
            ));
        }
    }

    /**
     *
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     * 
     * @param Request $request
     * @param int $propertyId
     * @return Response
     */
    public function wizardInvitedTenantAction(Request $request, $propertyId) {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();

        $property = $this->em->getRepository('ErpPropertyBundle:Property')->getPropertyByUser($user, $propertyId);
        $propertyFee = $this->get('erp.core.fee.service')->getPropertyFee();

        if (count($property->getInvitedUsers()) == 0) {
            $invitedUser = new InvitedUser();
            $invitedUser->setFirstName('');
            $invitedUser->setLastName('');
            $property->addInvitedUser($invitedUser);
        }

        $action = $this->generateUrl(
                'erp_property_listings_wizard_invited_tenant', ['propertyId' => $propertyId]
        );
        $formOptions = ['action' => $action, 'method' => 'POST'];
        $form = $this->createForm(new InviteTenantWizardCollectionFormType(), $property, $formOptions);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $xhr = $request->get('xhr', false);

                /** @var $property \Erp\PropertyBundle\Entity\Property */
                $invitedUsersWizard = $form->getData();

                foreach ($invitedUsersWizard->getInvitedUsers() as $k => $iu) {
                    $iu->setProperty($property);
                    $email = $iu->getInvitedEmail();
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $existUser = $this->em->getRepository('ErpUserBundle:User')->findOneBy(array('email' => $email));
                        $invitedUsers = $this->em->getRepository('ErpUserBundle:InvitedUser')->findOneBy(array('invitedEmail' => $email));
                        if (($existUser instanceof User && $existUser->isEnabled()) || $invitedUsers) {
                            /* CHECK BEFORE SHOWING FLASH MSG IF USER IS FOR UPDATE */
                            if ($iu->getProperty() && $iu->getProperty()->getId() != $propertyId) {
                                $this->get('session')->getFlashBag()
                                        ->add('alert_error', 'Tenant you are trying to add was already linked to other property');
                            }
                        } else {
                            if (($existUser instanceof User) && !$existUser->isEnabled() && $existUser->hasRole(User::ROLE_TENANT)) {
                                $userService = $this->get('erp.users.user.service');
                                $userService->activateUser($existUser);
                                $property->setTenantUser($existUser);
                                $this->em->persist($property->setStatus(Property::STATUS_RENTED));

                                $this->sendAssignTenantEmail($existUser);
                            } elseif (($existUser instanceof User) && !$existUser->isEnabled() && $existUser->hasRole(User::ROLE_MANAGER)) {
                                $this->get('session')->getFlashBag()
                                        ->add('alert_error', 'Email is disabled. Contact Administrator.');
                            } else {
                                //$invitedUser = $form->getData();
                                $iu->setProperty($property)
                                        ->setInviteCode($this->get('fos_user.util.token_generator')->generateToken())
                                        ->setIsUse(false);
                                $this->em->persist($iu);
                                $this->em->persist($property->setStatus(Property::STATUS_RENTED));
                                if ($k == 0) {
                                    $this->sendInviteTenantEmail($iu);
                                }
                            }
                        }
                    }
                }

                $this->em->persist($invitedUsersWizard);
                $this->em->flush();

                if ($xhr) {
                    $this->addFlash('alert_ok', 'Invited Tenant(s) successfully saved.');
                    return $this->redirect($this->generateUrl('erp_property_listings_all'));
                } else {
                    return $this->redirectToRoute('erp_property_listings_wizard_security_deposit', ['propertyId' => $property->getId()]);
                }
            } else {
                return new JsonResponse(array(
                    'success' => false,
                    'errors' => $form->getErrors(),
                ));
            }
        }
        if ($request->isXmlHttpRequest()) {
            return $this->render('ErpPropertyBundle:Wizard/blocks:invited-tenants.html.twig', [
                        'user' => $user,
                        'propertyFee' => $propertyFee,
                        'property' => $property,
                        'form' => $form->createView(),
                        'xhr' => true,
                        'propertyStatusRented' => Property::STATUS_RENTED,
                        'modalTitle' => 'Setup Invited Tenants',
                        'buttonLabel' => 'Submit'
            ]);
        } else {
            return $this->render('ErpPropertyBundle:Wizard:invited-tenants.html.twig', array(
                        'user' => $user,
                        'propertyFee' => $propertyFee,
                        'property' => $property,
                        'form' => $form->createView(),
                        'xhr' => false,
                        'propertyStatusRented' => Property::STATUS_RENTED,
                        'buttonLabel' => 'Next'
            ));
        }
    }

    /**
     * Security Deposit Page
     * 
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     *
     * @param Request $request
     * @param int $propertyId
     * @return Response
     */
    public function securityDepositAction(Request $request, $propertyId) {

        $user = $this->getUser();
        $xhr = $request->get('xhr', false);
        $publicToken = $request->get('publicToken', false);
        $accountId = $request->get('accountId', false);
        $property = $this->em->getRepository('ErpPropertyBundle:Property')->getPropertyByUser($user, $propertyId);

        $propertySecurityDeposit = $property->getSecurityDeposit() ?: new PropertySecurityDeposit();
        $property->setSecurityDeposit($propertySecurityDeposit);

        // GET BANK ACCOUNT INFORMATION
        $stripeAccount = $property->getDepositAccount() ?: new StripeDepositAccount();

        $property->setDepositAccount($stripeAccount);

        $action = $this->generateUrl('erp_property_listings_wizard_security_deposit', ['propertyId' => $property->getId()]);
        $formOptions = ['action' => $action, 'method' => 'POST'];
        $form = $this->createForm(new PropertySecurityDepositType(), $propertySecurityDeposit, $formOptions);
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->em->persist($property);
                $this->em->flush();
                if ($xhr) {
                    $this->addFlash('alert_ok', 'Property was saved successfully.');
                    return $this->redirect($this->generateUrl('erp_property_listings_all'));
                } else {
                    $this->addFlash('alert_ok', 'Property was saved successfully.');
                    return $this->redirect($this->generateUrl('erp_property_listings_all'));
                    //return $this->redirectToRoute('erp_property_listings_wizard_security_deposit', ['propertyId' => $property->getId()]);
                }
            }
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('ErpPropertyBundle:Wizard/blocks:security-deposit.html.twig', [
                        'form' => $form->createView(),
                        'user' => $user,
                        'property' => $property,
                        'xhr' => 1,
                        'modalTitle' => 'Security Deposit',
                        'buttonLabel' => 'Submit',
                        'bankAccount' => $stripeAccount,
            ]);
        } else {
            return $this->render('ErpPropertyBundle:Wizard:security-deposit.html.twig', array(
                        'user' => $user,
                        'form' => $form->createView(),
                        'property' => $property,
                        'buttonLabel' => 'Finish setup',
                        'xhr' => 0,
                        'bankAccount' => $stripeAccount,
            ));
        }
    }

    /**
     * Property search Page
     * 
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     *
     * @param Request $request
     * @return Response
     */
    public function searchAction(Request $request) {
        $user = $this->getUser();
        $properties = $user->getProperties()->filter(function ($property) {
            if ($property->getStatus() == Property::STATUS_DELETED) {
                $return = false;
            } else {
                $return = true;
            }
            return $return;
        });
        $result = [];
        foreach ($properties as $p) {
            $name = $p->getName();
            $name = !isset($name) || strlen($name) == 0 ? 'Empty Property' : $name;

            $result[] = ["id" => $p->getId(), "title" => $name];
        }
        return $this->render('ErpPropertyBundle:Wizard:property-search.html.twig', array(
                    'user' => $user,
                    'properties' => $result,
                    'property' => new Property()
        ));
    }

    /**
     * Remove tenant with status pending
     * 
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     *
     * @param Request $request
     * @param int $propertyId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function removeBankAccountAction(Request $request, $propertyId) {
        /** @var User $user */
        $user = $this->getUser();

        $askMsg = 'Are you sure you want to delete the Bank Information?';

        /** @var Property $property */
        $user = $this->getUser();
        $property = $this->em->getRepository('ErpPropertyBundle:Property')->getPropertyByUser($user, $propertyId);

        $propertySecurityDeposit = new PropertySecurityDeposit();
        $propertySecurityDeposit = $property->getSecurityDeposit() ?: new PropertySecurityDeposit();
        $property->setSecurityDeposit($propertySecurityDeposit);

        // GET BANK ACCOUNT INFORMATION
        $stripeAccount = $property->getDepositAccount();


        if (!$property) {
            throw new NotFoundHttpException('No permissions');
        }

        if ($request->getMethod() === 'DELETE') {
            if ($stripeAccount) {
                $property->setDepositAccount(null);
                $propertySecurityDeposit->setAddBankAccount(false);
                $this->em->persist($property);
                $this->em->remove($stripeAccount);
            }
            $this->em->flush();

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render('ErpCoreBundle:crossBlocks:delete-confirmation-popup.html.twig', array(
                    'askMsg' => $askMsg,
                    'actionUrl' => $this->generateUrl('erp_property_listings_wizard_remove_bank_account', array('propertyId' => $propertyId))
        ));
    }

    /**
     * 
     * @param User $landlord
     * @param User $manager
     * @return type
     */
    protected function sendInviteManagerEmail(User $landlord, User $manager) {
        $url = $this->generateUrl(
                'fos_user_registration_register',
                ['token' => $this->get('fos_user.util.token_generator')->generateToken()],
                UrlGeneratorInterface::ABSOLUTE_URL
        );
        $emailParams = array(
            'sendTo' => $manager->getEmail(),
            'url' => $url,
            'landlordInvite' => $landlord->__toString(),
            'landlordEmail' => $landlord->getEmail()
        );

        $sentStatus = $this->get('erp.core.email_notification.service')
                ->sendEmail(EmailNotificationFactory::TYPE_MANAGER_INVITE_LANDLORD, $emailParams);

        return $sentStatus;
    }

    /**
     * 
     * @param User $manager
     * @param User $landlord
     * @return type
     */
    protected function sendInviteLandlordEmail(User $manager, User $landlord) {
        $url = $this->generateUrl(
                'fos_user_registration_register',
                ['token' => $this->get('fos_user.util.token_generator')->generateToken()],
                UrlGeneratorInterface::ABSOLUTE_URL
        );
        $emailParams = array(
            'sendTo' => $landlord->getEmail(),
            'url' => $url,
            'managerInvite' => $manager->__toString(),
            'managerEmail' => $manager->getEmail()
        );

        $sentStatus = $this->get('erp.core.email_notification.service')
                ->sendEmail(EmailNotificationFactory::TYPE_LANDLORD_INVITE, $emailParams);

        return $sentStatus;
    }
    
    /**
     * 
     * @param InvitedUser $invitedUser
     * @return type
     */
    protected function sendInviteTenantEmail(InvitedUser $invitedUser) {
        $url = $this->generateUrl(
                'erp_user_tenant_registration', ['token' => $invitedUser->getInviteCode()], UrlGeneratorInterface::ABSOLUTE_URL
        );
        $emailParams = [
            'sendTo' => $invitedUser->getInvitedEmail(),
            'url' => $url,
            'invitedUser' => $invitedUser
        ];

        $sentStatus = $this->get('erp.core.email_notification.service')
                ->sendEmail(EmailNotificationFactory::TYPE_INVITE_TENANT_USER, $emailParams);

        return $sentStatus;
    }

    /**
     * 
     * @param Request $request
     * @param Property $property
     * @param type $formName
     * @param type $fields
     * @return type
     * @throws type
     */
    protected function preValidateFiles(Request $request, Property $property, $formName, $fields) {
        $files = $request->files->get($formName);
        $data = $request->request->get($formName);

        $errors = [];

        if (isset($files[$fields[0]]) && isset($data[$fields[0]])) {
            $files = $files[$fields[0]];

            $data = $data[$fields[0]];

            foreach ($data as $key => $item) {
                switch ($fields[0]) {
                    /* Documents */
                    case 'documents':
                        $file = new Document();
                        $file->setFile($files[$key][$fields[1]]);
                        $file->setOriginalName($item['originalName']);
                        $property->addDocument($file);
                        break;
                    /* Images */
                    case 'images':
                        $file = new Image();
                        $file->setImage($files[$key][$fields[1]]);
                        $property->addImage($file);
                        break;
                    /* Default */
                    default:
                        throw $this->createNotFoundException();
                }

                /** @var $errors \Symfony\Component\Validator\ConstraintViolationListInterface */
                $errorsValidate = $this->get('validator')->validate($file, null, ['EditProperty']);
                if ($errorsValidate->count()) {
                    unset($data[$key]);
                    unset($files[$key]);

                    $errors[] = $errorsValidate->get(0)->getMessage();
                }
            }

            $request->files->set($formName, [$fields[0] => $files]);
            $request->request->set($formName, [$fields[0] => $data]);
        }

        return ['request' => $request, 'errors' => $errors];
    }

    private function createBankAccountToken($publicToken, $accountId) {
        $itemPlaidService = $this->get('erp.payment.plaid.service.item');
        $processorPlaidService = $this->get('erp.payment.plaid.service.processor');

        $response = $itemPlaidService->exchangePublicToken($publicToken);
        $result = json_decode($response['body'], true);

        if ($response['code'] < 200 || $response['code'] >= 300) {
            throw new ServiceException($result['display_message']);
        }

        $response = $processorPlaidService->createBankAccountToken($result['access_token'], $accountId);
        $result = json_decode($response['body'], true);

        if ($response['code'] < 200 || $response['code'] >= 300) {
            throw new ServiceException($result['display_message']);
        }

        return $result['stripe_bank_account_token'];
    }

}
