<?php
namespace Erp\PropertyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PropertyImportFormType
 *
 * @package Erp\PropertyBundle\Form\Type
 */
class PropertyImportFormType extends AbstractType
{
    /** @var int */
    public static $maxSize = 51200; // 50 Kb

    /** @var array */
    public static $mimeTypes = [
        'text/csv',
        'text/plain'
    ];

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'file',
            'file',
            [
                'constraints' => [
                    new Assert\NotBlank([
                        'groups' => ['PropertyImport']
                    ]),
                    new Assert\File([
                        'maxSize' => self::$maxSize,
                        'mimeTypes' => self::$mimeTypes,
                        'mimeTypesMessage' => 'Wrong file format. Allowed file type is CSV',
                        'maxSizeMessage' => 'The file is too large. Allowed
                            maximum size is ' . (self::$maxSize/1024) . ' Kb',
                        'groups' => ['PropertyImport']
                    ])
                ],
                'label' => false,
                'required' => true,
                'mapped' => false,
                'attr' => ['class' => '_import_file', 'data-max-file-size' => self::$maxSize]
            ]
        );

        $builder->add(
            'submit',
            'submit',
            ['label' => 'Submit', 'attr' => ['class' => 'btn-circle']]
        );
    }


    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validation_groups' => ['PropertyImport']
        ]);
    }

    /**
     * Form name
     *
     * @return string
     */
    public function getName()
    {
        return 'erp_property_import_form';
    }
}
