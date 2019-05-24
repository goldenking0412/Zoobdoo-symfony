<?php
namespace Erp\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class EmailOptionsFormType extends AbstractType
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
     * Construct method
     */
    public function __construct()
    {
        $this->validationGroup = 'EmailOptions';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formBuilder = $builder;
        $this
            ->addSecondEmail()
        ;

        $this->formBuilder->add(
            'save',
            'submit',
            ['label' => 'Submit', 'attr' => ['class' => 'btn-circle']]
        );
    }

    /**
     * @return $this
     */
    private function addSecondEmail()
    {
        $this->formBuilder->add(
            'secondEmail',
            'email',
            [
                'label'              => 'Secondary Email',
                'label_attr'         => ['class' => 'control-label'],
                'attr'               => ['class' => 'form-control', 'maxlength' => 255],
                'required'           => false,
                'constraints'        => [
                    new Email(
                        [
                            'message' => 'This value is not a valid Email address.
                                          Use following formats: example@address.com',
                            'groups'  => [$this->validationGroup],
                        ]
                    ),
                ],
            ]
        );

        return $this;
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

    public function getName()
    {
        return 'erp_users_form_email_options';
    }
}
