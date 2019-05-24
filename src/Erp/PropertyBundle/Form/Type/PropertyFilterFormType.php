<?php
namespace Erp\PropertyBundle\Form\Type;

use Erp\PropertyBundle\Model\PropertyFilter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Class PropertyFilterFormType
 *
 * @package Erp\PropertyBundle\Form\Type
 */
class PropertyFilterFormType extends AbstractType
{
    /**
     * @var \Erp\PropertyBundle\Services\PropertyService
     */
    protected $propertyService;

    /**
     * @var \Erp\CoreBundle\Services\LocationService
     */
    protected $locationService;

    /**
     * @var FormBuilderInterface
     */
    protected $formBuilder;

    /**
     * @var string
     */
    protected $type;

    /**
     * @param ContainerInterface $container
     * @param string             $type
     */
    public function __construct(ContainerInterface $container, $type = PropertyFilter::FORM_SEARCH_TYPE)
    {
        $this->propertyService = $container->get('erp.property.service');
        $this->locationService = $container->get('erp.core.location');
        $this->type = $type;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formBuilder = $builder;
        $this->addState()
            ->addCity()
            ->addZip()
            ->addAddress()
            ->addBathrooms()
            ->addBedrooms()
            ->addSquareFootage()
            ->addOrder();

        $this->formBuilder->add('page', 'hidden');
        $this->formBuilder->add(
            'submit',
            'submit',
            ['label' => 'Search', 'attr' => ['class' => 'btn-circle']]
        );
    }


    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PropertyFilter::class,
        ]);
    }

    /**
     * Form name
     *
     * @return string
     */
    public function getName()
    {
        return 'properties';
    }

    /**
     * @return $this
     */
    private function addState()
    {
        $attr = ['class' => 'form-control select-control'];
        if ($this->type === PropertyFilter::FORM_AVAILABLE_TYPE) {
            $attr['data-class'] = 'states';
        }

        $this->formBuilder->add(
            'state',
            'choice',
            [
                'choices'    => $this->locationService->getStates(),
                'attr'       => $attr,
                'label'      => 'State',
                'label_attr' => ['class' => 'control-label'],
                'required'   => false,
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addCity()
    {
        $attr = ['class' => 'form-control select-control'];
        if ($this->type === PropertyFilter::FORM_AVAILABLE_TYPE) {
            $attr['data-class'] = 'cities';
        }

        $this->formBuilder->add(
            'cityId',
            'choice',
            [
                'choices'    => $this->locationService->getCities(),
                'attr'       => $attr,
                'label'      => 'City',
                'label_attr' => ['class' => 'control-label'],
                'required'   => false,
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addAddress()
    {
        $this->formBuilder->add(
            'address',
            'text',
            [
                'attr'        => ['class' => 'form-control', 'maxlength' => 255],
                'label'       => 'Street Address',
                'label_attr'  => ['class' => 'control-label'],
                'required'    => false,
                'constraints' => [
                    new Length(
                        [
                            'max'        => 255,
                            'maxMessage' => 'Address should have maximum 255 characters',
                        ]
                    )
                ],
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addZip()
    {
        $this->formBuilder->add(
            'zip',
            'text',
            [
                'attr'        => ['class' => 'form-control', 'maxlength' => 5],
                'label'       => 'Zip Code',
                'label_attr'  => ['class' => 'control-label'],
                'required'    => false,
                'constraints' => [
                    new Length(
                        [
                            'min'        => 5,
                            'max'        => 5,
                            'minMessage' => 'Zip code should have minimum 5 characters and maximum 5 characters',
                            'maxMessage' => 'Zip code should have minimum 5 characters and maximum 5 characters',
                        ]
                    ),
                    new Regex(
                        [
                            'pattern' => '/^[0-9]+$/',
                            'match'   => true,
                            'message' => "Zip code must contain numbers",
                        ]
                    )
                ],
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addBathrooms()
    {
        $this->formBuilder->add(
            'bathrooms',
            'choice',
            [
                'choices'    => $this->propertyService->getListOfBaths(),
                'attr'       => ['class' => 'form-control select-control'],
                'label'      => 'Bathrooms',
                'label_attr' => ['class' => 'control-label'],
                'required'   => false,
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addBedrooms()
    {
        $this->formBuilder->add(
            'bedrooms',
            'choice',
            [
                'choices'    => $this->propertyService->getListOfBeds(),
                'attr'       => ['class' => 'form-control select-control'],
                'label'      => 'Bedrooms',
                'label_attr' => ['class' => 'control-label'],
                'required'   => false,
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addSquareFootage()
    {
        $this->formBuilder->add(
            'squareFootage',
            'text',
            [
                'attr'        => ['class' => 'form-control half-input'],
                'label'       => 'Square Footage (min)',
                'label_attr'  => ['class' => 'control-label'],
                'required'    => false,
                'constraints' => [
                    new Length(
                        [
                            'max'        => 7,
                            'maxMessage' => 'Square Footage should have maximum 7 digits',
                        ]
                    ),
                    new Regex(
                        [
                            'pattern' => '/^[0-9]+$/',
                            'match'   => true,
                            'message' => "Square Footage must contain numbers",
                        ]
                    )
                ],
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addOrder()
    {
        $this->formBuilder->add(
            'order',
            'choice',
            [
                'choices'    => [
                    'updatedDate_desc'  => 'Newest to Oldest',
                    'updatedDate_asc'   => 'Oldest to Newest'
                ],
                'attr'       => ['class' => 'form-control select-control'],
                'label'      => 'Sorting',
                'label_attr' => ['class' => 'control-label'],
                'required'   => true,
            ]
        );

        return $this;
    }
}
