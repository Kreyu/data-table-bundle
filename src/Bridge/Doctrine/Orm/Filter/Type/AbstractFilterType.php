<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type;

use Doctrine\ORM\Query\Expr;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmProxyQuery;
use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Kreyu\Bundle\DataTableBundle\Filter\Type\AbstractFilterType as BaseAbstractType;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

abstract class AbstractFilterType extends BaseAbstractType
{
    public function apply(ProxyQueryInterface $query, FilterData $data, FilterInterface $filter, array $options): void
    {
        if (!$query instanceof DoctrineOrmProxyQuery) {
            throw new InvalidArgumentException(sprintf('Query must be an instance of "%s"', DoctrineOrmProxyQuery::class));
        }

        $operator = $this->getFilterOperator($data, $filter);
        $value = $this->getFilterValue($data);

        if (!in_array($operator, $filter->getConfig()->getSupportedOperators())) {
            return;
        }

        $queryPath = $this->getFilterQueryPath($query, $filter);

        $parameterName = $this->getUniqueParameterName($query, $filter);

        $expression = $this->getOperatorExpression($queryPath, $parameterName, $operator, new Expr());

        $query
            ->andWhere($expression)
            ->setParameter($parameterName, $this->getParameterValue($operator, $value));
    }

    protected function getFilterOperator(FilterData $data, FilterInterface $filter): Operator
    {
        return $data->getOperator() ?? $filter->getConfig()->getDefaultOperator();
    }

    protected function getFilterValue(FilterData $data): mixed
    {
        return $data->getValue();
    }

    /**
     * @throws InvalidArgumentException if operator is not supported by the filter
     */
    protected function getOperatorExpression(string $queryPath, string $parameterName, Operator $operator, Expr $expr): object
    {
        throw new InvalidArgumentException('Operator not supported');
    }

    /**
     * @param DoctrineOrmProxyQuery $query
     */
    public function getUniqueParameterName(ProxyQueryInterface $query, FilterInterface $filter): string
    {
        return $filter->getFormName().'_'.$query->getUniqueParameterId();
    }

    protected function getParameterValue(Operator $operator, mixed $value): mixed
    {
        return $value;
    }

    /**
     * @param DoctrineOrmProxyQuery $query
     */
    protected function getFilterQueryPath(ProxyQueryInterface $query, FilterInterface $filter): string
    {
        $rootAlias = current($query->getRootAliases());

        $queryPath = $filter->getQueryPath();

        if ($rootAlias && !str_contains($queryPath, '.') && $filter->getConfig()->getOption('auto_alias_resolving')) {
            $queryPath = $rootAlias.'.'.$queryPath;
        }

        return $queryPath;
    }

    public function getParent(): ?string
    {
        return DoctrineOrmFilterType::class;
    }
}
