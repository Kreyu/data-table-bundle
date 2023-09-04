<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Extension\HttpFoundation;

use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Extension\AbstractDataTableTypeExtension;
use Kreyu\Bundle\DataTableBundle\Request\HttpFoundationRequestHandler;
use Kreyu\Bundle\DataTableBundle\Request\RequestHandlerInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;

class HttpFoundationDataTableTypeExtension extends AbstractDataTableTypeExtension
{
    public function __construct(
        private readonly RequestHandlerInterface $requestHandler = new HttpFoundationRequestHandler(),
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