<?php

namespace Kreyu\Bundle\DataTableBundle\Extension\Core;

use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Extension\AbstractTypeExtension;
use Kreyu\Bundle\DataTableBundle\Request\RequestHandlerInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;

class HttpFoundationExtension extends AbstractTypeExtension
{
    public function __construct(
        private RequestHandlerInterface $requestHandler,
    ) {
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder->setRequestHandler($this->requestHandler);
    }

    public static function getExtendedTypes(): iterable
    {
        return [DataTableType::class];
    }
}
