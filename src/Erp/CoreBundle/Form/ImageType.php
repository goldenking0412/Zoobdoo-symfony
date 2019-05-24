<?php

namespace Erp\CoreBundle\Form;

use Erp\CoreBundle\Entity\Document;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Image as ConstraintsImage;
use Erp\CoreBundle\Entity\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ImageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'image',
            'file',
            [
                'data_class' => 'Erp\CoreBundle\Entity\Image',
                'constraints' => [
                    new ConstraintsImage([
                        'maxSize' => Image::$maxSize,
                        'mimeTypes' => Image::$allowedMimeTypes,
                        'groups' => [
                            'TenantDetails',
                            'ManagerDetails',
                            'EditProperty'
                        ],
                        'maxSizeMessage' => str_replace(
                            ['{maxSize}', '{sizeIn}'],
                            [(Image::$maxSize / 1024 / 1024), Document::SIZE_IN_MB],
                            Image::$maxSizeMessage
                        ),
                        'mimeTypesMessage' => Image::$mimeTypesMessage,
                    ]),
                ],
                'attr' => [
                    'class' => '_image',
                    'data-max-file-size' => Image::$maxSize,
                    'data-mime-types' => implode(';', Image::$allowedMimeTypes),
                    'data-max-file-size-message' => str_replace(
                        ['{maxSize}', '{sizeIn}'],
                        [(Image::$maxSize / 1024 / 1024), Document::SIZE_IN_MB],
                        Image::$maxSizeMessage
                    ),
                    'data-mime-types-message' => Image::$mimeTypesMessage,
                    'data-is-new' => '__is_new__',
                ],
                'label' => 'Picture',
                'required' => false
            ]
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Erp\CoreBundle\Entity\Image',
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'erp_corebundle_image';
    }
}
