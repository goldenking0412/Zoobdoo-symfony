<?php
namespace Erp\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Image;
use Erp\CoreBundle\Form\ImageType;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class TenantContactInfoFormType extends AddressDetailsFormType
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
        $this->validationGroup = 'TenantContactInfo';
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
            ->addWorkPhone()
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
        return 'erp_users_tenant_contact_info';
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
     * @return $this
     */
    protected function addWorkPhone()
    {
        $this->formBuilder->add(
            'workPhone',
            null,
            [
                'label'              => 'Work Phone Number',
                'label_attr'         => ['class' => 'control-label'],
                'attr'               => ['class' => 'form-control'],
                'required'           => false
            ]
        );

        return $this;
    }
}
