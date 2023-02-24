<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter\Form\Type;

use Kreyu\Bundle\DataTableBundle\Exporter\ExportData;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportStrategy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExportDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filename', TextType::class, [
                'data' => $options['default_filename'],
            ])
            ->add('exporter', ChoiceType::class, [
                'choices' => array_flip(array_map(
                    fn (ExporterInterface $exporter) => $exporter->getOption('label', $exporter->getName()),
                    $options['exporters'],
                )),
                'getter' => fn (ExportData $data) => $data->exporter->getName(),
                'setter' => fn (ExportData $data, mixed $exporter) => $data->exporter = $options['exporters'][$exporter],
            ])
            ->add('strategy', EnumType::class, [
                'class' => ExportStrategy::class,
            ])
            ->add('includePersonalization', CheckboxType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExportData::class,
            'translation_domain' => 'KreyuDataTable',
            'default_filename' => null,
            'exporters' => [],
        ]);

        $resolver->setAllowedTypes('exporters', ExporterInterface::class.'[]');
        $resolver->setAllowedTypes('default_filename', ['null', 'string']);
    }
}
