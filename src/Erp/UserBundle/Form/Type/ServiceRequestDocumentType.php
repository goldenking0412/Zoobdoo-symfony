<?php
namespace Erp\UserBundle\Form\Type;

use Erp\CoreBundle\Form\DocumentType;
use Erp\CoreBundle\Form\ImageType;
use Erp\UserBundle\Entity\ServiceRequest;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class EditDocumentsPropertyFormType
 *
 * @package Erp\PropertyBundle\Form\Type
 */
class ServiceRequestDocumentType extends AbstractType
{
    /**
     * @var FormBuilderInterface
     */
    protected $formBuilder;

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formBuilder = $builder;
        $this->formBuilder->add('token', null, ['mapped' => false]);
        $this->formBuilder->add(
            'documents',
            'collection',
            [
                'type' => new DocumentType(['inputClass' => 'form-control upload-input']),
                'required' => false,
                'allow_add'    => true,
                'allow_delete'  => true,
                'delete_empty'  => true,
                'by_reference' => false,
                'attr' => [
                    'nested_form' => true,
                    'nested_form_min' => 0,
                ],
            ]
        );

        $this->formBuilder->add(
            'submit',
            'submit',
            ['label' => 'Submit', 'attr' => ['class' => 'btn-circle']]
        );
    }

    /**
     * Form default options
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'        => 'Erp\UserBundle\Entity\ServiceRequest',
                'validation_groups' => ['EditProperty'],
                'csrf_protection'   => false,
            ]
        );
    }

    /**
     * Form name
     *
     * @return string
     */
    public function getName()
    {
        return 'erp_service_request_document_edit_form';
    }
}
