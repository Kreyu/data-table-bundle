<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ResolvedExporterType implements ResolvedExporterTypeInterface
{
    public function __construct(
        private ExporterTypeInterface $innerType,
        private ?ResolvedExporterTypeInterface $parent = null,
    ) {
    }

    public function getParent(): ?ResolvedExporterTypeInterface
    {
        return $this->parent;
    }

    public function getInnerType(): ExporterTypeInterface
    {
        return $this->innerType;
    }

    public function getOptionsResolver(): OptionsResolver
    {
        if (!isset($this->optionsResolver)) {
            if (null !== $this->parent) {
                $this->optionsResolver = clone $this->parent->getOptionsResolver();
            } else {
                $this->optionsResolver = new OptionsResolver();
            }

            $this->innerType->configureOptions($this->optionsResolver);
        }

        return $this->optionsResolver;
    }
}
