<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter\Type;

use Kreyu\Bundle\DataTableBundle\Exporter\ExporterBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Util\StringUtil;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractExporterType implements ExporterTypeInterface
{
    public function buildExporter(ExporterBuilderInterface $builder, array $options): void
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }

    public function getName(): string
    {
        return StringUtil::fqcnToShortName(static::class, ['ExporterType', 'Type']) ?: '';
    }

    public function getParent(): ?string
    {
        return ExporterType::class;
    }

    protected function getTempnam(array $options): string
    {
        return (new Filesystem())->tempnam($options['tempnam_dir'], $options['tempnam_prefix']);
    }
}
