<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterType;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

interface ExporterFactoryInterface
{
    /**
     * @param class-string<ExporterTypeInterface> $type
     *
     * @throws InvalidOptionsException if any of given option is not applicable to the given type
     */
    public function create(string $type = ExporterType::class, array $options = []): ExporterInterface;

    /**
     * @param class-string<ExporterTypeInterface> $type
     *
     * @throws InvalidOptionsException if any of given option is not applicable to the given type
     */
    public function createNamed(string $name, string $type = ExporterType::class, array $options = []): ExporterInterface;

    /**
     * @param class-string<ExporterTypeInterface> $type
     *
     * @throws InvalidOptionsException if any of given option is not applicable to the given type
     */
    public function createBuilder(string $type = ExporterType::class, array $options = []): ExporterBuilderInterface;

    /**
     * @param class-string<ExporterTypeInterface> $type
     *
     * @throws InvalidOptionsException if any of given option is not applicable to the given type
     */
    public function createNamedBuilder(string $name, string $type = ExporterType::class, array $options = []): ExporterBuilderInterface;
}
