<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class DateRangeType extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('from', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'autocomplete' => 'off',
                ],
            ])
            ->add('to', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'autocomplete' => 'off',
                ],
            ])
            ->setDataMapper($this)
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'kreyu_data_table_date_range';
    }

    public function mapDataToForms(mixed $viewData, \Traversable $forms): void
    {
        if (null === $viewData) {
            return;
        }

        $forms = iterator_to_array($forms);

        $forms['from']->setData($viewData['from']);
        $forms['to']->setData($viewData['to']);
    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData): void
    {
        $forms = iterator_to_array($forms);

        $from = $forms['from']->getData();
        $to = $forms['to']->getData();

        if (null === $from && null === $to) {
            return;
        }

        $viewData = [
            'from' => $forms['from']->getData(),
            'to' => $forms['to']->getData(),
        ];
    }
}
