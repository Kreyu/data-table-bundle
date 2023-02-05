<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter\Type;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractType implements ExporterTypeInterface
{
    public function configureOptions(OptionsResolver $resolver): void
    {
    }

    public function getParent(): ?string
    {
        return ExporterType::class;
    }

    protected function getTempnam(array $options): string
    {
        return (new Filesystem)->tempnam($options['tempnam_dir'], $options['tempnam_prefix']);
    }
}