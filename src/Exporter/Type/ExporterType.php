<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter\Type;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ExporterType implements ExporterTypeInterface
{
    public function export(DataTableView $view, array $options = []): File
    {
        throw new \LogicException('Base exporter type cannot be called directly');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'use_headers' => true,
            'label' => null,
            'tempnam_dir' => '/tmp',
            'tempnam_prefix' => 'exporter_',
        ]);
    }

    public function getParent(): ?string
    {
        return null;
    }
}
