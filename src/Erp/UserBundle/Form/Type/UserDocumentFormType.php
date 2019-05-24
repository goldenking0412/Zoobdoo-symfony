<?php
namespace Erp\UserBundle\Form\Type;

use Erp\CoreBundle\Form\DocumentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class UserDocumentFormType extends AbstractType
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
        $this->validationGroup = 'UserDocument';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formBuilder = $builder;
        $this->addDocument();

        $this->formBuilder->add(
            'save',
            'submit',
            ['label' => 'Submit', 'attr' => ['class' => 'btn btn-solid btn-solid-noborder file-upload-button']]
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'        => 'Erp\UserBundle\Entity\UserDocument',
                'validation_groups' => [$this->validationGroup]
            ]
        );
    }

    /**
     * Return form name
     *
     * @return string
     */
    public function getName()
    {
        return 'erp_user_document_form';
    }

    /**
     * @return $this
     */
    private function addDocument()
    {
        $this->formBuilder->add(
            'document',
            new DocumentType(),
            [
                'required' => false,
                'label' => 'Document',
            ]
        );

        return $this;
    }
}
