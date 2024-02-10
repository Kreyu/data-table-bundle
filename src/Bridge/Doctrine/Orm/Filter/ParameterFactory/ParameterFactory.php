<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ParameterFactory;

use Doctrine\ORM\Query\Parameter;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;

class ParameterFactory implements ParameterFactoryInterface
{
    public function create(DoctrineOrmProxyQueryInterface $query, FilterData $data, FilterInterface $filter): array
    {
        $parameters = [];

        $name = $filter->getName().'_'.$query->getUniqueParameterId();

        $value = $data->getValue();
        $operator = $data->getOperator();

        if (Operator::Between === $operator) {
            if (null !== $from = $value['from'] ?? null) {
                $parameters['from'] = new Parameter($name.'_from', $from);
            }

            if (null !== $to = $value['to'] ?? null) {
                $parameters['to'] = new Parameter($name.'_to', $to);
            }

            return $parameters;
        }

        $parameters[] = new Parameter($name, match ($operator) {
            Operator::Contains, Operator::NotContains => "%$value%",
            Operator::StartsWith => "$value%",
            Operator::EndsWith => "%$value",
            default => $value,
        });

        return $parameters;
    }
}
