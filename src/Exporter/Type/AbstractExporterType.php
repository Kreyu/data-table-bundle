<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter\Type;

use Kreyu\Bundle\DataTableBundle\Exporter\ExporterBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Util\StringUtil;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractExporterType implements ExporterTypeInterface
{
    public function buildExporter(ExporterBuilderInterface $builder, array $options): void {}

    public function configureOptions(OptionsResolver $resolver): void {}

    public function getName(): string
    {
        return StringUtil::fqcnToShortName(static::class, ['ExporterType', 'Type']) ?? '';
    }

    public function getParent(): ?string
    {
        return ExporterType::class;
    }

    /**
     * @param array<string, mixed> $options
     */
    protected function getTempnam(array $options): string
    {
        /** @var string $directory */
        $directory = $options['tempnam_dir'];

        /** @var string $prefix */
        $prefix = $options['tempnam_prefix'];

        return (new Filesystem())->tempnam($directory, $prefix);
    }
}
