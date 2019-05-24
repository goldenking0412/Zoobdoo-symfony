<?php

namespace Erp\UserBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Erp\StripeBundle\Form\Type\AbstractFilterType;
use Erp\UserBundle\Entity\Remittance;
use Erp\PropertyBundle\Entity\Property;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class RemittanceFilterType extends AbstractFilterType {

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $user = $this->tokenStorage->getToken()->getUser();

        $builder
                ->add('keyword', TextType::class, array(
                    'required' => false,
                    'label' => 'Search',
                    'attr' => array(
                        'placeholder' => 'Customer, Amount, Type, Description... etc.',
                    )
                ))
                ->add('dateFrom', DateType::class, array(
                    'required' => false,
                    'label' => 'Date From',
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'attr' => array(
                        'placeholder' => 'Date From',
                    )
                ))
                ->add('dateTo', DateType::class, array(
                    'required' => false,
                    'label' => 'Date To',
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'attr' => array(
                        'placeholder' => 'Date To',
                    )
                ))
                ->add('property', EntityType::class, array(
                    'required' => false,
                    'label' => 'Property',
                    'class' => Property::class,
                    'choices' => $user->getProperties()
                ))
                ->add('type', ChoiceType::class, array(
                    'required' => false,
                    'label' => 'Type',
                    'choices' => Remittance::getTypeOptions()
                ))
        ;
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
    
    public function getName() {
        return 'filter_remittances';
    }

}
