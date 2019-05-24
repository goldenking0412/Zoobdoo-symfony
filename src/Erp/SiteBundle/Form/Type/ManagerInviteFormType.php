<?php

namespace Erp\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ManagerInviteFormType extends AbstractType {

    /**
     * @var FormBuilderInterface
     */
    protected $formBuilder;

    /**
     * Construct method
     */
    public function __construct() {
        $this->validationGroup = 'ManagerInvite';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $this->formBuilder = $builder;
        $this
                ->addManagerFirstName()
                ->addManagerLastName()
                ->addManagerEmail()
                ->addTenantEmail()
                ->addMessage()
        ;

        $this->formBuilder->add('submit', SubmitType::class, array('label' => 'Send'));
    }

    /**
     * @return $this
     */
    public function addManagerFirstName() {
        $this->formBuilder->add('managerFirstName', TextType::class, array(
            'label' => false,
            'attr' => ['class' => 'form-control', 'placeholder' => 'Property Manager First Name'],
            'required' => true,
            'mapped' => false,
            'constraints' => array(
                new NotBlank([
                    'message' => 'Please enter Property Manager First Name',
                    'groups' => [$this->validationGroup],
                        ]),
                new Length([
                    'min' => 2,
                    'max' => 255,
                    'groups' => [$this->validationGroup],
                        ]),
            )
        ));

        return $this;
    }

    /**
     * @return $this
     */
    public function addManagerLastName() {
        $this->formBuilder->add('managerLastName', TextType::class, array(
            'attr' => ['class' => 'form-control', 'placeholder' => 'Property Manager Last Name',],
            'required' => true,
            'mapped' => false,
            'constraints' => array(
                new NotBlank([
                    'message' => 'Please enter Property Manager Last Name',
                    'groups' => [$this->validationGroup],
                        ]),
                new Length([
                    'min' => 2,
                    'max' => 255,
                    'groups' => [$this->validationGroup],
                        ]),
            )
        ));

        return $this;
    }

    /**
     * @return $this
     */
    public function addManagerEmail() {
        $this->formBuilder->add('managerEmail', \Symfony\Component\Form\Extension\Core\Type\EmailType::class, array(
            'attr' => ['class' => 'form-control', 'placeholder' => 'Property Manager Email'],
            'required' => true,
            'mapped' => false,
            'constraints' => array(
                new NotBlank([
                    'message' => 'Please enter Property Manager Email',
                    'groups' => [$this->validationGroup],
                        ]),
                new Email([
                    'message' => 'This value is not a valid Email address.
                                                      Use following formats: example@address.com',
                    'groups' => [$this->validationGroup],
                        ]),
                new Length([
                    'min' => 6,
                    'max' => 255,
                    'groups' => [$this->validationGroup],
                        ]),
            )
        ));

        return $this;
    }

    /**
     * @return $this
     */
    public function addTenantEmail() {
        $this->formBuilder->add('tenantEmail', \Symfony\Component\Form\Extension\Core\Type\EmailType::class, array(
            'attr' => ['class' => 'form-control', 'placeholder' => 'Tenant Email'],
            'required' => true,
            'mapped' => false,
            'constraints' => array(
                new NotBlank([
                    'message' => 'Please enter Tenant Email',
                    'groups' => [$this->validationGroup],
                        ]),
                new Email([
                    'message' => 'This value is not a valid Email address.
                                                      Use following formats: example@address.com',
                    'groups' => [$this->validationGroup],
                        ]),
                new Length([
                    'min' => 6,
                    'max' => 255,
                    'groups' => [$this->validationGroup],
                        ]),
            ),
        ));

        return $this;
    }

    /**
     * @return $this
     */
    public function addMessage() {
        $this->formBuilder->add('message', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, array(
            'attr' => ['class' => 'form-control', 'rows' => 8, 'placeholder' => 'Message', 'style' => 'resize: none;'],
            'required' => false,
            'mapped' => false,
            'constraints' => array(
                new Length([
                    'max' => 1000,
                    'groups' => [$this->validationGroup],
                        ]),
            ),
        ));

        return $this;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array('validation_groups' => [$this->validationGroup]));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'erp_site_send_invite_to_manager_form';
    }

}
