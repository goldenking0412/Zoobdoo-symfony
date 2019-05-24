<?php
namespace Erp\UserBundle\Form\Type;

use Erp\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Erp\CoreBundle\Form\ImageType;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class TenantDetailsFormType extends AbstractType
{
    /**
     * @var string
     */
    protected $validationGroup = 'TenantDetails';

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
        $this->validationGroup = 'TenantDetails';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formBuilder = $builder;
        $this
            ->addImage()
            ->addFirstName()
            ->addLastName()
            ->addOldPassword()
            ->addPlainPassword()
        ;

        $this->formBuilder->add(
            'save',
            'submit',
            ['label' => 'Submit', 'attr' => ['class' => 'btn-circle']]
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'        => 'Erp\UserBundle\Entity\User',
                'validation_groups' => [$this->validationGroup]
            ]
        );
    }

    public function getName()
    {
        return 'erp_users_tenant_form_details';
    }

    /**
     * @return $this
     */
    private function addImage()
    {
        $this->formBuilder->add(
            'image',
            new ImageType(),
            [
                'required' => false,
                'label' => 'Profile Picture'
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addFirstName()
    {
        $this->formBuilder->add(
            'firstName',
            'text',
            [
                'label'              => 'First Name',
                'label_attr'         => ['class' => 'control-label required-label'],
                'attr'               => ['class' => 'form-control'],
                'translation_domain' => $this->translationDomain,
                'required'           => true,
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addLastName()
    {
        $this->formBuilder->add(
            'lastName',
            'text',
            [
                'label'              => 'Last Name',
                'label_attr'         => ['class' => 'control-label required-label'],
                'attr'               => ['class' => 'form-control'],
                'translation_domain' => $this->translationDomain,
                'required'           => true,
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addOldPassword()
    {
        $this->formBuilder->add(
            'oldPassword',
            'password',
            [
                'label_attr' => ['class' => 'control-label'],
                'attr' => ['class' => 'form-control'],
                'mapped' => false,
                'required' => false
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addPlainPassword()
    {
        $this->formBuilder->add(
            'plainPassword',
            'repeated',
            [
                'type' => 'password',
                'options' => ['translation_domain' => $this->translationDomain],
                'first_options' => [
                    'label' => 'New Password',
                    'label_attr' => ['class' => 'control-label'],
                    'attr' => ['class' => 'form-control'],
                ],
                'second_options' => [
                    'label' => 'Repeat New Password',
                    'label_attr' => ['class' => 'control-label'],
                    'attr' => ['class' => 'form-control'],
                ],
                'constraints' => [
                    new Regex(
                        [
                            'pattern' => User::$passwordPattern,
                            'message' => User::$messagePasswordPattern,
                            'groups' => [$this->validationGroup],
                        ]
                    )
                ],
                'invalid_message' => 'Password mismatch',
                'trim'            => false,
                'required' => false
            ]
        );

        return $this;
    }
}
