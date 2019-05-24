<?php

namespace Erp\SmartMoveBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class SmartMoveExamFormType
 *
 * @package Erp\SmartMoveBundle\Form\Type
 */
class SmartMoveExamFormType extends AbstractType
{
    /**
     * @var string
     */
    protected $exam;

    /**
     * Construct method
     *
     * @param string $exam
     */
    public function __construct($exam)
    {
        $this->exam = json_decode($exam, true);
    }

    /**
     * Build form function
     *
     * @param FormBuilderInterface $builder the formBuilder
     * @param array                $options the options for this form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $questions = $this->exam['Questions'];

        if ($questions) {
            foreach ($questions as $question) {
                $choices = [];

                foreach ($question['Answers'] as $answer) {
                    $choices[$answer['Id']] = $answer['Text'];
                }

                $builder->add(
                    $question['Id'],
                    'choice',
                    [
                        'choices' => $choices,
                        'label' => $question['Text'],
                        'label_attr' => ['class' => 'control-label required-label'],
                        'attr' => ['class' => 'form-control', 'data-answer-id' => $question['Id']],
                        'required' => true,
                        'multiple' => false,
                        'expanded' => true,
                        'constraints' => [new NotBlank(['message' => 'Please answer the questions'])]
                    ]
                );
            }
        }
    }

    /**
     * Return unique name for this form
     *
     * @return string
     */
    public function getName()
    {
        return 'sm_exam_form';
    }
}
