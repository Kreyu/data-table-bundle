<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter\Form\Type;

use Kreyu\Bundle\DataTableBundle\Exporter\ExportData;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class ExportDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filename', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\Callback(function (string $filename): bool {
                        // TODO: return str_contains($filename, '');
                    })
                ],
            ])
            ->add('exporter', ChoiceType::class, [
                'choices' => array_flip(array_map(
                    fn (ExporterInterface $exporter) => $exporter->getConfig()->getOption('label', $exporter->getName()),
                    $options['exporters'],
                )),
            ])
            ->add('strategy', ExportStrategyType::class)
            ->add('includePersonalization', CheckboxType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => ExportData::class,
                'translation_domain' => 'KreyuDataTable',
                'exporters' => [],
            ])
            ->setAllowedTypes('exporters', ExporterInterface::class.'[]')
        ;
    }
}
