<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Extension\Core;

use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Extension\AbstractTypeExtension;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;
use Symfony\Component\Form\FormFactoryInterface;

class FiltrationFormFactoryExtension extends AbstractTypeExtension
{
    public function __construct(
        private FormFactoryInterface $formFactory,
    ) {
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->setFiltrationEnabled(true)
            ->setFiltrationFormFactory($this->formFactory)
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        return [DataTableType::class];
    }
}