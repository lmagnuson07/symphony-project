<?php

namespace App\Form;

use App\Entity\EdmontonPropertyAssessmentData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotEqualTo;
use Symfony\Component\Validator\Constraints\Positive;

class EpadEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('accountNumber', TextType::class, [
                'label' => 'Account Number',
                'attr' => [
                    'readonly' => true
                ],
                'disabled' => true
            ])
            ->add('suite', TextType::class, [
                'trim' => true,
                'required' => false,
                'constraints' => [
                    new Positive(),
                ]
            ])
            ->add('houseNumber', TextType::class, [
                'label' => 'House Number',
                'trim' => true,
                'required' => true
            ])
            ->add('streetName', TextType::class, [
                'label' => 'Street Name',
                'trim' => true,
                'required' => true
            ])
            ->add('garage', CheckboxType::class, [
                'label' => 'Has Garage',
                'required' => false
            ])
            ->add('neighbourhoodId', IntegerType::class, [
                'label' => 'Neighbourhood ID',
                'required' => false,
                'constraints' => [
                    new Positive(),
                    new Length(['min' => 4, 'max' => 4]),
                ]
            ])
            ->add('neighbourhood', TextType::class, [
                'required' => false,
                'trim' => true,
            ])
            ->add('ward', TextType::class, [
                'required' => false,
                'trim' => true,
            ])
            ->add('assessedValue', IntegerType::class, [
                'label' => 'Assessed Value',
                'required' => true,
                'constraints' => [
                    new Positive(),
                    new GreaterThanOrEqual(5000),
                ]
            ])
            ->add('latitude', NumberType::class, [
                'required' => true,
                'constraints' => [
                    new NotEqualTo(0),
                ]
            ])
            ->add('longitude', NumberType::class, [
                'required' => true,
                'constraints' => [
                    new NotEqualTo(0),
                ]
            ])
            ->add('assessmentClass1', TextType::class, [
                'required' => false,
                'trim' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EdmontonPropertyAssessmentData::class,
        ]);
    }
}
