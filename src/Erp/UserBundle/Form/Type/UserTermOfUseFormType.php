<?php

namespace Erp\UserBundle\Form\Type;

use Erp\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserTermOfUseFormType extends AbstractType
{
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
     * Construct method
     */
    public function __construct()
    {
        $this->validationGroup = 'UserTermOfUse';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formBuilder = $builder;
        $this->formBuilder->add(
            'isTermOfUse',
            'checkbox',
            [
                'required' => true,
            ]
        )
        ->add(
            'save',
            'submit',
            ['label' => 'Accept', 'attr' => ['class' => 'btn submit-popup-btn',
                'disabled' => 'disabled'
            ]]
        );
    }


    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => [$this->validationGroup],
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'erp_users_term_of_use';
    }


}
