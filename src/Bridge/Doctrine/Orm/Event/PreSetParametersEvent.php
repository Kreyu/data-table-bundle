<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Event;

use Doctrine\ORM\Query\Parameter;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

class PreSetParametersEvent extends DoctrineOrmFilterEvent
{
    public function __construct(
        ProxyQueryInterface $query,
        FilterData $data,
        FilterInterface $filter,
        private array $parameters,
    ) {
        parent::__construct($query, $data, $filter);
    }

    /**
     * @return array<Parameter>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }
}
