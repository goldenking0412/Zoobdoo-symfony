<?php
namespace Erp\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Doctrine\ORM\EntityRepository;

class AddressDetailsFormType extends AbstractType
{
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
    public function __construct(Request $request, $states = null)
    {
        $this->request = $request;
        $this->states = $states;
        $this->validationGroup = 'AddressDetails';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formBuilder = $builder;
        $this
            ->addAddressOne()
            ->addAddressTwo()
            ->addState()
            ->addCity()
            ->addPostalCode()
            ->addPhone()
            ->addWebsiteUrl()
        ;

        $this->formBuilder->add(
            'save',
            'submit',
            ['label' => 'Submit', 'attr' => ['class' => 'btn-circle']]
        );
    }

    /**
     * Return form name
     *
     * @return string
     */
    public function getName()
    {
        return 'erp_users_form_address_details';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'validation_groups' => [$this->validationGroup]
            ]
        );
    }

    /**
     * Return USA states
     *
     * @return array
     */
    protected function getStates()
    {
        return $this->states;
    }

    /**
     * @return $this
     */
    protected function addAddressOne()
    {
        $this->formBuilder->add(
            'addressOne',
            'textarea',
            [
                'label'              => 'Address',
                'label_attr'         => ['class' => 'control-label required-label'],
                'attr'               => ['class' => 'form-control'],
                'required'           => true
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    protected function addAddressTwo()
    {
        $this->formBuilder->add(
            'addressTwo',
            'textarea',
            [
                'label'              => 'Address 2',
                'label_attr'         => ['class' => 'control-label'],
                'attr'               => ['class' => 'form-control'],
                'required'           => false,
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    protected function addCity()
    {
        $formData = $this->request->request->get($this->getName());

        $currentState = $this->formBuilder->getData()->getState();
        $selectedState = $formData['state'];
        $selectedState = ($selectedState)
            ? $selectedState
            : $currentState
        ;

        $this->formBuilder->add(
            'city',
            'entity',
            [
                'label' => 'City',
                'class' => 'Erp\CoreBundle\Entity\City',
                'label_attr' => ['class' => 'control-label required-label'],
                'attr' => [
                    'class' => 'form-control select-control',
                    'aria-labelledby' => "dLabel",
                    'data-class' => 'cities'
                ],
                'query_builder' => function (EntityRepository $er) use ($currentState, $selectedState) {
                    $state = ($currentState !== $selectedState)
                        ? $selectedState
                        : $currentState
                    ;

                    return $er->getCitiesByStateCodeQb($state);
                }
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    protected function addState()
    {
        $this->formBuilder->add(
            'state',
            'choice',
            [
                'label' => 'State',
                'label_attr' => ['class' => 'control-label required-label'],
                'attr' => [
                    'class' => 'form-control select-control',
                    'aria-labelledby' => "dLabel",
                    'data-class' => 'states'
                ],
                'choices' => $this->getStates(),
                'multiple' => false,
                'required' => true,
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    protected function addPostalCode()
    {
        $this->formBuilder->add(
            'postalCode',
            'text',
            [
                'label'              => 'ZIP',
                'label_attr'         => ['class' => 'control-label required-label'],
                'attr'               => ['class' => 'form-control'],
                'required'           => true
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    protected function addPhone()
    {
        $this->formBuilder->add(
            'phone',
            null,
            [
                'label'              => 'Phone Number',
                'label_attr'         => ['class' => 'control-label required-label'],
                'attr'               => ['class' => 'form-control', 'placeholder' => '555-555-5555'],
                'required'           => true
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    protected function addWebsiteUrl()
    {
        $this->formBuilder->add(
            'websiteUrl',
            'url',
            [
                'label'              => 'Website',
                'label_attr'         => ['class' => 'control-label'],
                'attr'               => ['class' => 'form-control contact-email full-width'],
                'required'           => false
            ]
        );

        return $this;
    }
}
