<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExportData
{
    public string $filename;
    public ExporterInterface $exporter;
    public ExportStrategy $strategy;
    public bool $includePersonalization;

    public static function fromArray(array $data): self
    {
        $resolver = (new OptionsResolver())
            ->setRequired('exporter')
            ->setDefaults([
                'filename' => 'export',
                'strategy' => ExportStrategy::INCLUDE_CURRENT_PAGE,
                'include_personalization' => false,
            ])
            ->setAllowedTypes('exporter', ExporterInterface::class)
            ->setAllowedTypes('filename', 'string')
            ->setAllowedTypes('strategy', ['string', ExportStrategy::class])
            ->setAllowedTypes('include_personalization', 'bool')
            ->setNormalizer('strategy', function (Options $options, $value): ExportStrategy {
                return $value instanceof ExportStrategy ? $value : ExportStrategy::from($value);
            })
        ;

        $data = array_intersect_key($data, array_flip($resolver->getDefinedOptions()));

        $data = $resolver->resolve($data);

        $exportData = new self();
        $exportData->filename = $data['filename'];
        $exportData->exporter = $data['exporter'];
        $exportData->strategy = $data['strategy'];
        $exportData->includePersonalization = $data['include_personalization'];

        return $exportData;
    }
}
