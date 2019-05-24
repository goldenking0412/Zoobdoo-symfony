<?php

namespace Erp\CoreBundle\Form;

use Erp\CoreBundle\Entity\Document;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class DocumentType extends AbstractType
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $fieldClass = isset($this->options['inputClass']) ? $this->options['inputClass'] : '';

        $builder->add(
            'file',
            'file',
            [
                'label' => false,
                'data_class'  => null,
                'constraints' => [
                    new NotBlank(['message' => 'File is not selected.', 'groups' => ['UserDocument']]),
                    new File(
                        [
                            'maxSize' => Document::$maxSize,
                            'mimeTypes' => Document::$mimeTypes,
                            'mimeTypesMessage' => Document::$mimeTypesMessage,
                            'maxSizeMessage' => str_replace(
                                ['{maxSize}', '{sizeIn}'],
                                [(Document::$maxSize / 1024 / 1024), Document::SIZE_IN_MB],
                                Document::$maxSizeMessage
                            ),
                            'groups' => ['UserDocument', 'EditProperty']
                        ]
                    ),
                ],
                'attr' => [
                    'class' => '_file',
                    'data-max-file-size' => Document::$maxSize,
                    'data-mime-types' => implode(';', Document::$mimeTypes),
                    'data-max-file-size-message' => str_replace(
                        ['{maxSize}', '{sizeIn}'],
                        [(Document::$maxSize / 1024 / 1024), Document::SIZE_IN_MB],
                        Document::$maxSizeMessage
                    ),
                    'data-mime-types-message' => Document::$mimeTypesMessage,
                ],
            ]
        )->add(
            'originalName',
            'text',
            [
                'label' => false,
                'attr' => ['class' => $fieldClass, 'readonly' => 'readonly'],
                'required' => true,
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'Document name cannot be empty.',
                            'groups' => ['UserDocument', 'EditProperty']
                        ]
                    ),
                    new Regex(
                        [
                            'pattern' => Document::$patternFilename,
                            'message' => Document::$messagePatternFilename,
                            'groups' => ['UserDocument', 'EditProperty']
                        ]
                    )
                ]
            ]
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Erp\CoreBundle\Entity\Document',
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'erp_corebundle_document';
    }
}
