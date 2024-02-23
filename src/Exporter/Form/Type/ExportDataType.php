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
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ExportDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filename', TextType::class, [
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\Callback(function (?string $value, ExecutionContextInterface $context) {
                        if (null === $value) {
                            return;
                        }

                        if (str_contains($value, '/') || str_contains($value, '\\')) {
                            $context->buildViolation('The filename cannot contain following characters: "\/"')
                                ->addViolation()
                            ;
                        }
                    }),
                ],
            ])
            ->add('exporter', ChoiceType::class, [
                'choices' => array_flip(array_map(
                    fn (ExporterInterface $exporter) => $exporter->getConfig()->getOption('label') ?? $exporter->getName(),
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
        $resolver->setDefaults([
            'data_class' => ExportData::class,
            'translation_domain' => 'KreyuDataTable',
            'exporters' => [],
        ]);

        $resolver->setAllowedTypes('exporters', ExporterInterface::class.'[]');

        // TODO: Remove deprecated default filename option
        $resolver->setDefault('default_filename', null);
        $resolver->setDeprecated('default_filename', 'kreyu/data-table-bundle', '0.14', 'The "%name%" option is deprecated.');
    }
}
