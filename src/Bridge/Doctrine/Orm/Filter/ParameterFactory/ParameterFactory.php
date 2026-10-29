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

        /** @var mixed $value */
        $value = $data->getValue();
        $operator = $data->getOperator();

        if ($operator === Operator::Between) {
            /** @var mixed $from */
            $from = $value['from'] ?? null;

            /** @var mixed $to */
            $to = $value['to'] ?? null;

            if ($from !== null) {
                $parameters['from'] = new Parameter($name.'_from', $from);
            }

            if ($to !== null) {
                $parameters['to'] = new Parameter($name.'_to', $to);
            }

            return $parameters;
        }

        $parameters[] = new Parameter($name, match ($operator) {
            Operator::Contains, Operator::NotContains => "%{$value}%",
            Operator::StartsWith => "{$value}%",
            Operator::EndsWith => "%{$value}",
            default => $value,
        });

        return $parameters;
    }
}
