<?php

namespace Erp\PropertyBundle\Form\Type;

use Erp\CoreBundle\Form\DocumentType;
use Erp\CoreBundle\Form\ImageType;
use Erp\PropertyBundle\Entity\Property;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class EditPropertyFormType
 *
 * @package Erp\PropertyBundle\Form\Type
 */
class EditPropertyFormType extends AbstractType {

    /**
     * @var FormBuilderInterface
     */
    protected $formBuilder;

    /**
     * @var \Erp\CoreBundle\Services\LocationService
     */
    protected $locationService;

    /**
     * @var \Erp\PropertyBundle\Services\PropertyService
     */
    protected $propertyService;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $this->locationService = $container->get('erp.core.location');
        $this->propertyService = $container->get('erp.property.service');
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $this->formBuilder = $builder;
        $this->addName()
                ->addType()
                ->addStateAndCityElements()
                ->addAddress()
                ->addZip()
                ->addPrice()
                ->addOfBeds()
                ->addOfBaths()
                ->addStatus()
                ->addSquareFootage()
                ->addAmenities()
                ->addAboutProperties()
                ->addAdditionalDetails()
                ->addImages();

        $this->formBuilder->add(
                'submit', 'submit', ['label' => 'Submit', 'attr' => ['class' => 'blue']]
        );
    }

    /**
     * Form default options
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults([
            'data_class' => 'Erp\PropertyBundle\Entity\Property',
            'validation_groups' => ['EditProperty']
        ]);
    }

    /**
     * Form name
     *
     * @return string
     */
    public function getName() {
        return 'erp_property_edit_form';
    }

    /**
     * @return $this
     */
    private function addName() {
        $this->formBuilder->add('name', 'text', [
            'label' => 'Property Name',
            'required' => true
        ]);

        return $this;
    }

    /**
     * Add city and state
     *
     * @return $this
     */
    private function addStateAndCityElements() {
        $this->formBuilder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData']);
        $this->formBuilder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);

        return $this;
    }

    /**
     * @param FormEvent $event
     */
    public function onPreSetData(FormEvent $event) {
        $state = $event->getData()->getStateCode();
        $this->locationService->setCities($state);
        $this->addElements($event->getForm(), $state);
    }

    /**
     * @param FormEvent $event
     */
    public function onPreSubmit(FormEvent $event) {
        $state = $event->getData()['stateCode'];
        $this->locationService->setCities($state);
        $this->addElements($event->getForm(), $state);
    }

    /**
     * @param FormInterface $form
     * @param null          stateCode
     */
    private function addElements(FormInterface $form, $stateCode = null) {
        $form->add('stateCode', 'choice', [
            'choices' => $this->locationService->getStates(),
            'attr' => ['class' => 'select-control', 'data-class' => 'states'],
            'label' => 'State',
            'required' => false,
            'multiple' => false
        ]);

        $form->add('city', 'entity', [
            'label' => 'City',
            'class' => 'Erp\CoreBundle\Entity\City',
            'attr' => ['class' => 'select-control', 'data-class' => 'cities'],
            'required' => false,
            'query_builder' => function (EntityRepository $er) use ($stateCode) {
                return $er->getCitiesByStateCodeQb($stateCode);
            }
        ]);
    }

    /**
     * @return $this
     */
    private function addAddress() {
        $this->formBuilder->add('address', 'text', [
            'label' => 'Address',
            'required' => true
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    private function addZip() {
        $this->formBuilder->add('zip', 'text', [
            'label' => 'Zip',
            'required' => true
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    private function addPrice() {
        $this->formBuilder->add(
                $this->formBuilder->create('settings', 'form', [
                            'data_class' => \Erp\PropertyBundle\Entity\PropertySettings::class,
                        ])
                        ->add('paymentAmount', 'money', [
                            'currency' => false,
                            'label' => 'Rent',
                            'attr' => ['placeholder' => '$0.00'],
                            'required' => true,
                            'constraints' => [
                                new NotBlank(
                                        [
                                    'message' => 'Please enter Price',
                                    'groups' => ['EditProperty']
                                        ]
                                )
                            ]
                        ])
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addOfBeds() {
        $this->formBuilder->add('ofBeds', 'choice', [
            'label' => '# of Beds',
            'choices' => $this->propertyService->getListOfBeds(),
            'attr' => ['class' => 'select-control'],
            'required' => false
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    private function addOfBaths() {
        $this->formBuilder->add('ofBaths', 'choice', [
            'label' => '# of Baths',
            'choices' => $this->propertyService->getListOfBaths(),
            'attr' => ['class' => 'select-control'],
            'required' => false
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    private function addStatus() {
        $attributes = ['class' => 'select-control'];

        $choices = [
            Property::STATUS_DRAFT => 'Draft (saved, not published)',
            Property::STATUS_RENTED => 'Rented (not published)',
            Property::STATUS_AVAILABLE => 'Available (published on the website)',
        ];

        $this->formBuilder->add('status', 'choice', [
            'label' => 'Status',
            'choices' => $choices,
            'attr' => $attributes,
            'required' => true,
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    private function addType() {
        $attributes = ['class' => 'select-control'];

        $choices = [
            Property::TYPE_APARTMENT,
            Property::TYPE_SINGLE_FAMILY_HOME,
            Property::TYPE_DUPLEX_TRIPLEX_TOWNHOME,
            Property::TYPE_MOBILE_HOME,
            Property::TYPE_STUDENT_HOUSING,
            Property::TYPE_COMMERCIAL,
        ];

        $this->formBuilder->add('type', 'choice', [
            'label' => 'Property Type',
            'choices' => array_combine($choices, $choices),
            'attr' => $attributes,
            'required' => true,
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    private function addSquareFootage() {
        $this->formBuilder->add('squareFootage', 'number', [
            'attr' => ['maxlength' => '7', 'placeholder' => '59'],
            'label' => 'Square Footage',
            'required' => true
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    private function addAmenities() {
        $this->formBuilder->add('amenities', 'textarea', [
            'label' => 'Amenities',
            'required' => false
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    private function addAboutProperties() {
        $this->formBuilder->add('aboutProperties', 'textarea', [
            'label' => 'About Property',
            'required' => false
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    private function addAdditionalDetails() {
        $this->formBuilder->add('additionalDetails', 'textarea', [
            'label' => 'Additional Details',
            'required' => false
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    private function addImages() {
        $this->formBuilder->add('images', 'collection', [
            'type' => new ImageType(),
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'by_reference' => false,
            'label_attr' => [
                'type' => 'images'
            ],
            'label' => 'Images',
        ]);

        return $this;
    }

}
