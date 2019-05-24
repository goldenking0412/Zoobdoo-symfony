<?php

namespace Erp\UserBundle\Form\Type;

use Symfony\Component\Validator\Constraints as Assert;
use Erp\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Erp\CoreBundle\Form\ImageType;
use Erp\PaymentBundle\Entity\StripeAccount;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Erp\StripeBundle\Form\Type\AccountVerificationType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class ManagerRegistrationFormType extends AbstractType {

    /**
     * @var string
     */
    protected $validationGroup = '';

    /**
     * @var string
     */
    protected $translationDomain = 'FOSUserBundle';

    /**
     * @var FormBuilderInterface
     */
    protected $formBuilder;

    /**
     * @var array
     */
    protected $states;

    /**
     * @var bool
     */
    protected $invitedUser;

    /**
     * Construct method
     */
    public function __construct(Request $request, $states = null, $invitedUser = false) {
        $this->request = $request;
        $this->states = $states;
        $this->invitedUser = $invitedUser;
        $this->validationGroup = 'ManagerRegister';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $this->formBuilder = $builder;
        $this
                ->addRole()
                ->addFirstName()
                ->addLastName()
                ->addPhone()
                ->addWebsiteUrl()
                ->addAddressOne()
                //->addAddressTwo()
                ->addCity()
                ->addState()
                ->addPostalCode()
                ->addEmail()
                ->addPlainPassword()
                ->addIsTermOfUse()
        ;

        $this->formBuilder
                ->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'))
                ->add('stripeAccount', AccountVerificationType::class, [
                    // 'entry_options' => array('parentData' => $options['data']),
                    'constraints' => new Assert\Valid(),
                    'label' => false,
                    'data_class' => StripeAccount::class,
                    'data' => new StripeAccount()
                ])
                ->add(
                        'save', 'submit', ['label' => 'Submit', 'attr' => ['class' => 'action-button']]
                )
        ;
    }

    /**
     * 
     * @param FormEvent $event
     */
    public function onPreSetData(FormEvent $event) {
        $user = $event->getData();

        if ($user && $user->hasRole(User::ROLE_MANAGER)) {
            $this
                    ->addCompanyName()
                    ->addLogo()
            ;
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => User::class,
            'validation_groups' => ['UserTermOfUse', 'ManagerRegister']
        ));
    }

    public function getName() {
        return 'erp_users_manager_form_registration';
    }

    /**
     * Return USA states
     *
     * @return array
     */
    protected function getStates() {
        return $this->states;
    }

    /**
     * @return $this
     */
    private function addCompanyName() {
        $this->formBuilder->add('companyName', TextType::class, [
            'label' => 'Company Name',
            'label_attr' => ['class' => 'control-label'],
            'attr' => ['class' => 'form-control contact-email full-width'],
            'translation_domain' => $this->translationDomain,
            'required' => false
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    private function addFirstName() {
        $this->formBuilder->add('firstName', TextType::class, [
            'label' => 'First Name',
            'label_attr' => ['class' => 'control-label required-label'],
            'attr' => ['class' => 'form-control'],
            'translation_domain' => $this->translationDomain,
            'required' => true,
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    private function addLastName() {
        $this->formBuilder->add('lastName', TextType::class, [
            'label' => 'Last Name',
            'label_attr' => ['class' => 'control-label required-label'],
            'attr' => ['class' => 'form-control'],
            'translation_domain' => $this->translationDomain,
            'required' => true,
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    private function addPhone() {
        $this->formBuilder->add('phone', TextType::class, [
            'label' => 'Phone Number',
            'label_attr' => ['class' => 'control-label required-label'],
            'attr' => ['class' => 'form-control', 'placeholder' => '555-555-5555'],
            'translation_domain' => $this->translationDomain,
            'required' => true
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    private function addWebsiteUrl() {
        $this->formBuilder->add('websiteUrl', UrlType::class, [
            'label' => 'Website',
            'label_attr' => ['class' => 'control-label'],
            'attr' => ['class' => 'form-control contact-email full-width'],
            'translation_domain' => $this->translationDomain,
            'required' => false
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    private function addAddressOne() {
        $this->formBuilder->add('addressOne', TextareaType::class, [
            'label' => 'Address',
            'label_attr' => ['class' => 'control-label required-label'],
            'attr' => ['class' => 'form-control'],
            'translation_domain' => $this->translationDomain,
            'required' => true
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    private function addAddressTwo() {
        $this->formBuilder->add('addressTwo', TextareaType::class, [
            'label' => 'Address 2',
            'label_attr' => ['class' => 'control-label'],
            'attr' => ['class' => 'form-control'],
            'translation_domain' => $this->translationDomain,
            'required' => false
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    private function addCity() {
        $formData = $this->request->request->get($this->getName());

        $currentState = $this->formBuilder->getData()->getState();
        $selectedState = ($formData['state']) ? $formData['state'] : $currentState;

        $this->formBuilder->add('city', EntityType::class, [
            'label' => 'City',
            'class' => 'Erp\CoreBundle\Entity\City',
            'label_attr' => ['class' => 'control-label required-label'],
            'attr' => [
                'class' => 'form-control select-control',
                'aria-labelledby' => 'dLabel',
                'data-class' => 'cities'
            ],
            'query_builder' => function (EntityRepository $er) use ($currentState, $selectedState) {
                $state = ($currentState !== $selectedState) ? $selectedState : $currentState
                ;

                return $er->getCitiesByStateCodeQb($state);
            },
            'translation_domain' => $this->translationDomain,
            'required' => true
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    private function addState() {
        $this->formBuilder->add('state', ChoiceType::class, [
            'label' => 'State',
            'label_attr' => ['class' => 'control-label required-label'],
            'attr' => [
                'class' => 'form-control select-control',
                'aria-labelledby' => 'dLabel',
                'data-class' => 'states'
            ],
            'choices' => $this->getStates(),
            'multiple' => false,
            'required' => false,
            'translation_domain' => $this->translationDomain,
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    private function addPostalCode() {
        $this->formBuilder->add('postalCode', TextType::class, [
            'label' => 'ZIP',
            'label_attr' => ['class' => 'control-label required-label'],
            'attr' => ['class' => 'form-control'],
            'translation_domain' => $this->translationDomain,
            'required' => true
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    private function addEmail() {
        if ($this->invitedUser) {
            $disabled = true;
        } else {
            $disabled = false;
        }

        $this->formBuilder->add('email', EmailType::class, [
            'label' => 'Email',
            'label_attr' => ['class' => 'control-label required-label'],
            'attr' => ['class' => 'form-control'],
            'translation_domain' => $this->translationDomain,
            'invalid_message' => 'Email is invalid',
            'required' => true,
            'disabled' => $disabled,
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Please enter your Email',
                    'groups' => [$this->validationGroup],
                ]),
                new Assert\Length([
                    'min' => 6,
                    'max' => 255,
                    'minMessage' => 'Email should have minimum 6 characters and maximum 255 characters',
                    'maxMessage' => 'Email should have minimum 6 characters and maximum 255 characters',
                    'groups' => [$this->validationGroup],
                ]),
                new Assert\Email([
                    'message' => 'This value is not a valid Email address.
                                          Use following formats: example@address.com',
                    'groups' => [$this->validationGroup],
                ]),
            ],
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    private function addIsTermOfUse() {
        $this->formBuilder->add('isTermOfUse', CheckboxType::class, [
            'required' => true,
            'label' => 'Terms of use'
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    private function addPlainPassword() {
        $this->formBuilder->add('plainPassword', RepeatedType::class, [
            'type' => 'password',
            'options' => ['translation_domain' => $this->translationDomain],
            'first_options' => [
                'label' => 'Password',
                'label_attr' => ['class' => 'control-label required-label'],
                'attr' => ['class' => 'form-control']
            ],
            'second_options' => [
                'label' => 'Repeat Password',
                'label_attr' => ['class' => 'control-label required-label'],
                'attr' => ['class' => 'form-control']
            ],
            'invalid_message' => 'Password mismatch',
            'trim' => false,
            'required' => true,
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Please enter your password',
                    'groups' => [$this->validationGroup],
                ]),
                new Assert\Regex([
                    'pattern' => '/^(?=.{5,255})(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W])(?!.*\s).*$/',
                    'message' => 'The password must contain letters, numbers and
                                special characters (example: &#@%!$) and must not have spaces',
                    'groups' => [$this->validationGroup],
                ])
            ],
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    private function addLogo() {
        $this->formBuilder->add('logo', ImageType::class, [
            'required' => false,
            'label' => 'Company logo'
        ]);
    }

    private function addRole() {
        $choices = ($this->invitedUser)
                ? array(User::ROLE_TENANT => 'Tenant')
                : array(User::ROLE_MANAGER => 'Manager', User::ROLE_LANDLORD => 'Landlord');

        $this->formBuilder->add('role', ChoiceType::class, [
            'label' => 'Account type',
            'label_attr' => ['class' => 'control-label required-label'],
            'attr' => [
                'class' => 'form-control select-control'
            ],
            'choices' => $choices,
            'multiple' => false,
            'required' => true,
            'translation_domain' => $this->translationDomain,
            'mapped' => false
        ]);

        return $this;
    }

}
