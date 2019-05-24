<?php
namespace Erp\UserBundle\Form\Type;

use Erp\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Doctrine\ORM\EntityRepository;

class UserResetPasswordFormType extends AbstractType
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
        $this->validationGroup = 'ResetPassword';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formBuilder = $builder;

        $this->formBuilder->add(
            'plainPassword',
            'repeated',
            [
                'type'            => 'password',
                'options'         => ['translation_domain' => $this->translationDomain],
                'first_options'   => [
                    'label'      => 'Password',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => ['class' => 'form-control'],
                ],
                'second_options'  => [
                    'label'      => 'Repeat Password',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => ['class' => 'form-control'],
                ],
                'constraints' => [
                    new Regex(
                        [
                            'pattern' => User::$passwordPattern,
                            'message' => User::$messagePasswordPattern,
                            'groups'     => [$this->validationGroup],
                        ]
                    )
                ],
                'invalid_message' => 'Password mismatch',
                'trim'            => false,
                'required' => false
            ]
        );

        $this->formBuilder->add(
            'save',
            'submit',
            ['label' => 'Submit', 'attr' => ['class' => 'btn submit-popup-btn']]
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
                'validation_groups' => ['ResetPassword']
            ]
        );
    }

    public function getName()
    {
        return 'erp_users_form_reset_password';
    }
}
