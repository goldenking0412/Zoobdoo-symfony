<?php

namespace Erp\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class TenantFormType extends AddressDetailsFormType {

    /**
     * @var string
     */
    protected $validationGroup = '';

    /**
     * @var FormBuilderInterface
     */
    protected $formBuilder;

    /**
     * @var array
     */
    protected $states;

    /**
     * @var \Erp\UserBundle\Form\Type\Request
     */
    protected $request;

    /**
     * Construct method
     */
    public function __construct(Request $request, $states = null) {
        $this->request = $request;
        $this->states = $states;
        $this->validationGroup = 'TenantEdit';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $this->formBuilder = $builder;
        $this
                ->addFirstName()
                ->addLastName()
                ->addEmail()
                ->addPhone()
                ->addAddressOne()
                ->addAddressTwo()
                ->addState()
                ->addCity()
                ->addPostalCode()
                ->addWorkPhone()
                ->addWebsiteUrl()
        ;

        // $this->formBuilder->add('save', 'submit', array('label' => 'Submit', 'attr' => array('class' => 'btn submit-popup-btn')));
    }

    /**
     * Return form name
     *
     * @return string
     */
    public function getName() {
        return 'erp_users_tenant_contact_info';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'validation_groups' => array($this->validationGroup)
        ));
    }

    /**
     * @return $this
     */
    protected function addWorkPhone() {
        $this->formBuilder->add('workPhone', null, array(
            'label' => 'Work Phone Number',
            'label_attr' => ['class' => 'control-label'],
            'attr' => ['class' => 'form-control'],
            'required' => false
        ));

        return $this;
    }

    /**
     * @return $this
     */
    private function addFirstName() {
        $this->formBuilder->add('firstName', 'text', array(
            'label' => 'First Name',
            'label_attr' => ['class' => 'control-label required-label'],
            'attr' => ['class' => 'form-control'],
            'required' => true,
        ));

        return $this;
    }

    /**
     * @return $this
     */
    private function addLastName() {
        $this->formBuilder->add('lastName', 'text', array(
            'label' => 'Last Name',
            'label_attr' => ['class' => 'control-label required-label'],
            'attr' => ['class' => 'form-control'],
            'required' => true,
        ));

        return $this;
    }

    /**
     * @return $this
     */
    private function addEmail() {
        $this->formBuilder->add('email', 'email', array(
            'required' => true,
            'attr' => array('class' => 'form-control'),
            'constraints' => array(
                new NotBlank(
                        array('message' => 'Email cannot be empty.', 'groups' => array($this->validationGroup))
                ),
                new Email(array('message' => 'Lanlord must have valid email.', 'groups' => array($this->validationGroup))
                )
            )
        ));

        return $this;
    }

}
