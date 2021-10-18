<?php

namespace App\Form;

use App\SearchFormData\SearchFormData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('q', null, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'api platform...'
                ]
            ])
            ->add('order', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'order ASC' => 'asc',
                    'order DESC' => 'desc'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'method' => 'GET',
            'data_class' => SearchFormData::class,
        ]);
    }


    public function getBlockPrefix(): string
    {
        return '';
    }
}
