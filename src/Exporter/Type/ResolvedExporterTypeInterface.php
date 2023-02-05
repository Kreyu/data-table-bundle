<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface ResolvedExporterTypeInterface
{
    public function getParent(): ?ResolvedExporterTypeInterface;

    public function getInnerType(): ExporterTypeInterface;

    public function getOptionsResolver(): OptionsResolver;
}