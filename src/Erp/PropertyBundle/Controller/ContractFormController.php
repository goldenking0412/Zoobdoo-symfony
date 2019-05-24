<?php

namespace Erp\PropertyBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\CoreBundle\Entity\Document;
use Erp\PropertyBundle\Entity\ContractForm;
use Erp\PropertyBundle\Repository\ContractFormRepository;
use Erp\PropertyBundle\Entity\ContractSection;
use Erp\PropertyBundle\Repository\ContractSectionRepository;
use Erp\PropertyBundle\Entity\Property;
use Erp\PropertyBundle\Form\Type\ContractSectionFormType;
use Erp\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class ContractFormController
 *
 * @package Erp\PropertyBundle\Controller
 */
class ContractFormController extends BaseController {

    const PDF_WIDTH_BALANCER = 9;
    const PDF_FONT_SIZE_PARAM = '8';
    const PDF_FONT_NAME_PARAM = 'Times New Roman';
    const PDF_FOOTER_RIGHT = 'Manager ____________ Tenant ____________    [page] / [topage]  ';

    /** @var string */
    protected $pdfDir = '/cache/pdf_files';

    /** @var string */
    protected $publicDir = '/web';

    /** @var string */
    protected $rootDir;

    /** @var int */
    protected $fieldsCounter;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->rootDir = $this->container->getParameter('kernel.root_dir');
    }

    /**
     * Render contract constructor
     *
     * @param Request $request
     * @param         $propertyId
     *
     * @return RedirectResponse|Response
     */
    public function constructorAction(Request $request, $propertyId) {
        /** @var Property $property */
        $property = $this->getProperty($propertyId);

        $contractFormRepository = $this->em->getRepository('ErpPropertyBundle:ContractForm');
        $contractForm = $contractFormRepository->findOneBy(['property' => $property]);

        // Cloning contract form, yet if not exists
        if (!$contractForm) {
            $contractForm = $this->getCloneContractForm($property);
        }

        $contractSection = new ContractSection();
        $contractSectionForm = $this->createContractSectionForm($contractSection, $property);

        if ($request->getMethod() == 'POST') {
            $contractSectionForm->handleRequest($request);

            if ($contractSectionForm->isValid()) {
                /** @var ContractSectionRepository $contractSectionRepository */
                $contractSectionRepository = $this->em->getRepository('ErpPropertyBundle:ContractSection');
                $sortNumber = $contractSectionRepository->getSortNumber($contractForm);

                $contractSection->setContractForm($contractForm);
                $contractSection->setSort($sortNumber);

                $this->em->persist($contractSection);
                $this->em->flush();

                $this->addFlash('alert_ok', 'Section was added successfully.');

                return $this->redirectToRoute('erp_property_contract_form', ['propertyId' => $property->getId()]);
            }
        }

        return $this->render('ErpPropertyBundle:ContractForm:constructor.html.twig', [
                    'user' => $property->getUser(),
                    'property' => $property,
                    'contractForm' => $contractForm,
                    'contractSectionForm' => $contractSectionForm->createView(),
        ]);
    }

    /**
     * Set publish/not publish form
     *
     * @param Request $request
     * @param int     $propertyId
     *
     * @return JsonResponse
     */
    public function publishFormAction(Request $request, $propertyId) {
        /** @var Property $property */
        $property = $this->getProperty($propertyId);

        $contractForm = $this->em->getRepository('ErpPropertyBundle:ContractForm')
                ->findOneBy(['property' => $property]);

        if (!$contractForm) {
            return new JsonResponse(['error' => 'Contract Form not found']);
        }

        $isPublished = (bool) $request->request->get('isPublished', false);

        $contractForm->setIsPublished($isPublished);

        $this->em->flush($contractForm);

        return new JsonResponse(['status' => true, 'isPublished' => $isPublished]);
    }

    /**
     * Render fill contract form
     *
     * @param Request $request
     * @param         $propertyId
     *
     * @return Response
     */
    public function fillAction(Request $request, $propertyId) {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        /** @var Property $property */
        $property = $this->getProperty($propertyId);

        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Profile', $this->get('router')->generate('erp_user_profile_dashboard'));
        $breadcrumbs->addItem('Documentation', $this->get('router')->generate('erp_user_documentation'));
        $breadcrumbs->addItem('Online Rental Contract');

        $contractFormRepository = $this->em->getRepository('ErpPropertyBundle:ContractForm');

        if ($user->hasRole(User::ROLE_MANAGER)) {
            $contractForm = $contractFormRepository->findOneBy(['property' => $property]);
        } else {
            $contractForm = $contractFormRepository->findOneBy(['property' => $property, 'isPublished' => true]);
        }

        if (!$contractForm) {
            throw $this->createNotFoundException('Contract Form not found');
        }

        if ($request->getMethod() == 'POST') {
            $formData = $request->request->all();

            $sections = $this->parseContractForm($contractForm, $formData);

            $template = $this->renderView('ErpPropertyBundle:ContractForm:pdf-template.html.twig', [
                'property' => $property,
                'sections' => $sections,
            ]);

            $pdfFileName = 'RentalContract_' . time() . '.pdf';

            if (array_key_exists('sign', $formData)) {
                $document = $this->generatePdfFromHtml($pdfFileName, $template, $property);

                // TODO: Adapt to Hello Sign
                $this->performHelloSign($user, $document);
                return $this->redirectToRoute('erp_property_contract_form_fill', ['propertyId' => $propertyId]);
            } else {
                return $this->generatePdfFromHtml($pdfFileName, $template, $property, true);
            }
        }

        $sections = $this->parseContractForm($contractForm);

        return $this->render('ErpPropertyBundle:ContractForm:fill.html.twig', [
                    'user' => $property->getUser(),
                    'property' => $property,
                    'sections' => $sections,
                    'isManager' => $user->hasRole(User::ROLE_MANAGER),
        ]);
    }

    /**
     * Charge for create online rental contract form
     *
     * @param Request $request
     * @param int $propertyId
     *
     * @return RedirectResponse|Response
     */
    public function chargeAction(Request $request, $propertyId) {
        $property = $this->getProperty($propertyId);

        $contractFormRepository = $this->em->getRepository('ErpPropertyBundle:ContractForm');
        $contractForm = $contractFormRepository->findOneBy(array('property' => $property));

        if ($contractForm) {
            return $this->redirectToRoute('erp_property_contract_form', ['propertyId' => $propertyId]);
        }

        $amount = $this->get('erp.core.fee.service')->getCreateContractFormFee();

        if ($request->getMethod() == Request::METHOD_POST) {
            $user = $property->getUser();
            
            $paymentResponse = $this->get('erp.payment.service')->makeSinglePayment(null, $amount, array(), null, $user->getStripeCustomer($user->getPrimaryPaymentType()));

            if (!$paymentResponse->isSuccess()) {
                $this->addFlash(
                        'alert_error', 'Payment of Contract Form failed due ' . $paymentResponse->getErrorMessage()
                );
                $redirect = $this->redirectToRoute(
                        'erp_property_listings_edit_documents', ['propertyId' => $propertyId]
                );
            } else {
                $this->addFlash(
                        'alert_ok', 'Payment of Contract Form successfully transfered'
                );
                $this->em->persist(
                        $property->getUser()->setContractFormCounter(
                                $property->getUser()->getContractFormCounter() + 1
                        )
                );
                $this->em->flush();
                $redirect = $this->redirectToRoute('erp_property_contract_form', ['propertyId' => $propertyId]);
            }

            return $redirect;
        }

        return $this->render('ErpCoreBundle:crossBlocks:general-confirmation-popup.html.twig', [
                    'actionUrl' => $this->generateUrl('erp_property_contract_form_charge', ['propertyId' => $propertyId]),
                    'actionBtn' => 'Yes',
                    'cancelBtn' => 'No',
                    'askMsg' => 'You will be charged $' . $amount . ' for this feature. Do you want to proceed?',
        ]);
    }

    /**
     * Update data contract section
     *
     * @param Request $request
     * @param int     $propertyId
     * @param int     $sectionId
     *
     * @return JsonResponse
     */
    public function updateSectionAction(Request $request, $propertyId, $sectionId) {
        $contractSection = $this->getContractSection($propertyId, $sectionId);

        $content = $request->get('content', null);

        if ($content) {
            $contractSection->setContent($content);
        }

        $validator = $this->get('validator');
        $errors = $validator->validate($contractSection, null, ['ContractSection']);

        if (count($errors)) {
            return new JsonResponse(['errors' => 'The data isn\'t correct']);
        }

        $this->em->persist($contractSection);
        $this->em->flush();

        return new JsonResponse(['status' => true]);
    }

    /**
     * Remove contract section
     *
     * @param Request   $request
     * @param int       $propertyId
     * @param int       $sectionId
     *
     * @return JsonResponse
     */
    public function removeSectionAction(Request $request, $propertyId, $sectionId) {
        $contractSection = $this->getContractSection($propertyId, $sectionId);

        if ($request->getMethod() == 'DELETE') {
            $this->em->remove($contractSection);
            $this->em->flush();

            return $this->redirectToRoute('erp_property_contract_form', ['propertyId' => $propertyId]);
        }

        return $this->render('ErpCoreBundle:crossBlocks:delete-confirmation-popup.html.twig', [
                    'askMsg' => 'Are you sure you want to delete this section?',
                    'actionUrl' => $this->generateUrl(
                            'erp_property_contract_remove_section', ['propertyId' => $propertyId, 'sectionId' => $sectionId]
                    )
        ]);
    }

    /**
     * Get clone contract form
     *
     * @param Property $property
     *
     * @return null|object
     */
    public function getCloneContractForm(Property $property) {
        /** @var ContractFormRepository $contractFormRepository */
        $contractFormRepository = $this->em->getRepository('ErpPropertyBundle:ContractForm');
        $contractForm = $contractFormRepository->findPrevFormByUser($property->getUser());

        if (!$contractForm) {
            $contractForm = $contractFormRepository->findOneBy(['isDefault' => true, 'property' => null]);

            if (!$contractForm) {
                throw new NotFoundHttpException('Default application form not found');
            }
        }

        $this->cloneContractForm($contractForm, $property);
        $this->em->clear();

        $applicationForm = $contractFormRepository->findOneBy(['property' => $property]);

        return $applicationForm;
    }

    /**
     * Clone contract form
     *
     * @param ContractForm $contractForm
     * @param Property     $property
     *
     * @return ContractForm
     */
    protected function cloneContractForm(ContractForm $contractForm, Property $property) {
        /** @var ContractForm $contractFormClone */
        $contractFormClone = clone $contractForm;
        $contractFormClone->setProperty($property);

        $this->em->persist($contractFormClone);
        $this->em->flush();

        return $contractFormClone;
    }

    /**
     * Return property
     *
     * @param int $propertyId
     *
     * @return Property
     */
    protected function getProperty($propertyId) {
        /** @var User $user */
        $user = $this->getUser();

        $propertyRepository = $this->em->getRepository('ErpPropertyBundle:Property');

        /** @var Property $property */
        $property = ($user instanceof User and $user->hasRole(User::ROLE_MANAGER)) ? $propertyRepository->findOneBy(['id' => $propertyId, 'user' => $user]) : $propertyRepository->find($propertyId);

        if (!$property instanceof Property) {
            throw new NotFoundHttpException('Property not found');
        }

        return $property;
    }

    /**
     * Return contract section
     *
     * @param int $propertyId
     * @param int $sectionId
     *
     * @return ContractSection
     */
    protected function getContractSection($propertyId, $sectionId) {
        $property = $this->getProperty($propertyId);

        /** @var ContractFormRepository $contractFormRepository */
        $contractFormRepository = $this->em->getRepository('ErpPropertyBundle:ContractForm');
        $contractForm = $contractFormRepository->findOneBy(['property' => $property]);

        /** @var ContractSectionRepository $contractSectionRepository */
        $contractSectionRepository = $this->em->getRepository('ErpPropertyBundle:ContractSection');
        /** @var ContractSection $contractSection */
        $contractSection = $contractSectionRepository->findOneBy(['id' => $sectionId, 'contractForm' => $contractForm]);

        if (!$contractSection) {
            throw new NotFoundHttpException('Section not found');
        }

        return $contractSection;
    }

    /**
     * Create contract section form
     *
     * @param ContractSection $contractSection
     * @param Property        $property
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createContractSectionForm(ContractSection $contractSection, Property $property) {
        $action = $this->generateUrl('erp_property_contract_form', ['propertyId' => $property->getId()]);
        $formOptions = ['action' => $action, 'method' => 'POST'];
        $form = $this->createForm(new ContractSectionFormType(), $contractSection, $formOptions);

        return $form;
    }

    /**
     * Parse contract form
     *
     * @param ContractForm $contractForm
     * @param array        $formData
     *
     * @return array
     */
    protected function parseContractForm(ContractForm $contractForm, $formData = []) {
        $this->fieldsCounter = 0;
        $sections = [];
        if ($contractForm && $contractForm->getContractSections()) {
            /** @var ContractSection $contractSection */
            foreach ($contractForm->getContractSections() as $contractSection) {
                $sections[] = $this->parseContractSection($contractSection, $formData);
            }
        }

        return $sections;
    }

    /**
     * Parse contract section
     *
     * @param ContractSection $contractSection
     * @param array           $formData
     *
     * @return ContractSection
     */
    protected function parseContractSection(ContractSection $contractSection, $formData = []) {


        // inputs
        $content = preg_replace_callback(
                '/([_]{2,})/', function ($matches) use ($formData) {
            $this->fieldsCounter++;
            $fieldName = 'input_' . $this->fieldsCounter;

            if ($formData) {
                $replacement = $this->renderView('ErpPropertyBundle:ContractForm/elements:input-pdf-mask.html.twig', [
                    'width' => strlen($matches[0]) * self::PDF_WIDTH_BALANCER,
                    'value' => $formData[$fieldName],
                ]);
            } else {
                $replacement = $this->renderView('ErpPropertyBundle:ContractForm/elements:input.html.twig', [
                    'name' => $fieldName,
                    'width' => strlen($matches[0]),
                ]);
            }

            return $replacement;
        }, $contractSection->getContent()
        );

        // checkboxes
        $content = preg_replace_callback(
                '/(\[\])/', function () use ($formData) {
            $this->fieldsCounter++;
            $fieldName = 'checkbox_' . $this->fieldsCounter;

            if ($formData) {
                $replacement = (array_key_exists($fieldName, $formData)) ? '&#10003;' : '&#10008;';
            } else {
                $replacement = $this->renderView('ErpPropertyBundle:ContractForm/elements:checkbox.html.twig', [
                    'name' => $fieldName,
                ]);
            }

            return $replacement;
        }, $content
        );

        // marked display in contract form
        $content = preg_replace_callback(
                '/(\{marked-symbol\})/', function () use ($formData) {
            if ($formData) {
                $replacement = '&#10003;';
            } else {
                $replacement = '&#10003;';
            }

            return $replacement;
        }, $content
        );

        // un-marked display in contract form
        $content = preg_replace_callback(
                '/(\{unmarked-symbol\})/', function () use ($formData) {
            if ($formData) {
                $replacement = '&#10008;';
            } else {
                $replacement = '&#10008;';
            }

            return $replacement;
        }, $content
        );

        // inputs date
        $content = preg_replace_callback(
                '/(\{date\})/', function ($matches) use ($formData) {
            $this->fieldsCounter++;
            $fieldName = 'input_' . $this->fieldsCounter;

            if ($formData) {
                $replacement = $this->renderView('ErpPropertyBundle:ContractForm/elements:input-pdf-mask.html.twig', [
                    'width' => strlen($matches[0]) * self::PDF_WIDTH_BALANCER,
                    'value' => $formData[$fieldName],
                ]);
            } else {
                $replacement = $this->renderView('ErpPropertyBundle:ContractForm/elements:input_date.html.twig', [
                    'name' => $fieldName,
                    'width' => strlen($matches[0]),
                ]);
            }

            return $replacement;
        }, $content
        );

        // inputs number
        $content = preg_replace_callback(
                '/(\{number\})/', function ($matches) use ($formData) {
            $this->fieldsCounter++;
            $fieldName = 'input_' . $this->fieldsCounter;

            if ($formData) {
                $replacement = $this->renderView('ErpPropertyBundle:ContractForm/elements:input-pdf-mask.html.twig', [
                    'width' => strlen($matches[0]) * self::PDF_WIDTH_BALANCER,
                    'value' => $formData[$fieldName],
                ]);
            } else {
                $replacement = $this->renderView('ErpPropertyBundle:ContractForm/elements:input_number.html.twig', [
                    'name' => $fieldName,
                    'width' => strlen($matches[0]),
                ]);
            }

            return $replacement;
        }, $content
        );


        // inputs time
        $content = preg_replace_callback(
                '/(\{time\})/', function ($matches) use ($formData) {
            $this->fieldsCounter++;
            $fieldName = 'input_' . $this->fieldsCounter;

            if ($formData) {
                $replacement = $this->renderView('ErpPropertyBundle:ContractForm/elements:input-pdf-mask.html.twig', [
                    'width' => strlen($matches[0]) * self::PDF_WIDTH_BALANCER,
                    'value' => $formData[$fieldName],
                ]);
            } else {
                $replacement = $this->renderView('ErpPropertyBundle:ContractForm/elements:input_time.html.twig', [
                    'name' => $fieldName,
                    'width' => strlen($matches[0]),
                ]);
            }

            return $replacement;
        }, $content
        );

        // marked display in contract form
        $content = preg_replace_callback(
            '/(\{marked-symbol\})/',
            function () use ($formData) {
                if ($formData) {
                    $replacement = '&#10003;';
                } else {
                    $replacement = '&#10003;';
                }

                return $replacement;
            },
            $content
        );

        // un-marked display in contract form
        $content = preg_replace_callback(
            '/(\{unmarked-symbol\})/',
            function () use ($formData) {
                if ($formData) {
                    $replacement = '&#10008;';
                } else {
                    $replacement = '&#10008;';
                }

                return $replacement;
            },
            $content
        );

        // inputs date
        $content = preg_replace_callback(
            '/(\{date\})/',
            function ($matches) use ($formData) {
                $this->fieldsCounter++;
                $fieldName = 'input_' . $this->fieldsCounter;

                if ($formData) {
                    $replacement = $this->renderView(
                        'ErpPropertyBundle:ContractForm/elements:input-pdf-mask.html.twig',
                        [
                            'width' => strlen($matches[0]) * self::PDF_WIDTH_BALANCER,
                            'value' => $formData[$fieldName],
                        ]
                    );
                } else {
                    $replacement = $this->renderView(
                        'ErpPropertyBundle:ContractForm/elements:input_date.html.twig',
                        [
                            'name' => $fieldName,
                            'width' => strlen($matches[0]),
                        ]
                    );
                }

                return $replacement;
            },
            $content
        );

        // inputs number
        $content = preg_replace_callback(
            '/(\{number\})/',
            function ($matches) use ($formData) {
                $this->fieldsCounter++;
                $fieldName = 'input_' . $this->fieldsCounter;

                if ($formData) {
                    $replacement = $this->renderView(
                        'ErpPropertyBundle:ContractForm/elements:input-pdf-mask.html.twig',
                        [
                            'width' => strlen($matches[0]) * self::PDF_WIDTH_BALANCER,
                            'value' => $formData[$fieldName],
                        ]
                    );
                } else {
                    $replacement = $this->renderView(
                        'ErpPropertyBundle:ContractForm/elements:input_number.html.twig',
                        [
                            'name' => $fieldName,
                            'width' => strlen($matches[0]),
                        ]
                    );
                }

                return $replacement;
            },
            $content
        );


        // inputs time
        $content = preg_replace_callback(
            '/(\{time\})/',
            function ($matches) use ($formData) {
                $this->fieldsCounter++;
                $fieldName = 'input_' . $this->fieldsCounter;

                if ($formData) {
                    $replacement = $this->renderView(
                        'ErpPropertyBundle:ContractForm/elements:input-pdf-mask.html.twig',
                        [
                            'width' => strlen($matches[0]) * self::PDF_WIDTH_BALANCER,
                            'value' => $formData[$fieldName],
                        ]
                    );
                } else {
                    $replacement = $this->renderView(
                        'ErpPropertyBundle:ContractForm/elements:input_time.html.twig',
                        [
                            'name' => $fieldName,
                            'width' => strlen($matches[0]),
                        ]
                    );
                }

                return $replacement;
            },
            $content
        );

        return $content;
    }

    /**
     * Generate pdf file from html
     *
     * @param string    $fileName
     * @param string    $html
     * @param Property  $property
     * @param bool      $download
     *
     * @return Document
     */
    protected function generatePdfFromHtml($fileName, $html, Property $property, $download = false) {
        $document = new Document();
        $document
                ->setName($fileName)
                ->setPath(substr($this->pdfDir, 1, strlen($this->pdfDir)))
                ->setOriginalName($fileName)
        ;

        $this->pdfDir = $this->rootDir . '/..' . $this->publicDir . $this->pdfDir;

        if (!file_exists($this->pdfDir)) {
            mkdir($this->pdfDir);
        }

        $params = [
            'footer-font-name' => self::PDF_FONT_NAME_PARAM,
            'footer-font-size' => self::PDF_FONT_SIZE_PARAM,
            'footer-left' =>
            '   Property: ' . $property->getStateCode()
            . ', ' . $property->getCityName()
            . ', ' . $property->getAddress()
            ,
            'footer-right' =>
            utf8_decode(self::PDF_FOOTER_RIGHT),
        ];

        if (!$download) {
            $this->get('knp_snappy.pdf')->generateFromHtml($html, $this->pdfDir . '/' . $fileName, $params);

            return $document;
        } else {
            $pdf = $this->get('knp_snappy.pdf')->getOutputFromHtml($html, $params);

            return $this->pdfResponse($pdf, $fileName);
        }
    }

    /**
     * Perform Hello Sign
     *
     * @param User     $user
     * @param Document $document
     *
     * @return RedirectResponse
     */
    private function performHelloSign(User $user, Document $document) {
        /* TODO ADAPT TO HELLO SIGN */
        /* if ($charge) {
            $email = $user->getEmail();
            $response = $this->get('erp.signature.docusign.service')->createEnvelopeFromDocument($document, $email);

            if (isset($response->status) && $response->status == 'sent') {
                $this->addFlash(
                        'alert_ok', 'Document to be signed is sent to your email, please check ' . $email . '.'
                );

                return $this->redirectToRoute('erp_user_profile_dashboard');
            } else {
                $msg = 'Please try again later or contact your administrator';
                $this->addFlash(
                        'alert_error', isset($response->message) ? $response->message : $msg
                );
            }
        } else {
            $this->addFlash(
                    'alert_error', 'An error occurred while trying to sign a document. Please contact your Manager.'
            );
        } */

        return;
    }

}
