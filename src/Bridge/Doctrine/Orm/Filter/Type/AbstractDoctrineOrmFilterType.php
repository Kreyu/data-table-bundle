<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type;

use Doctrine\ORM\Query\Expr;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Kreyu\Bundle\DataTableBundle\Filter\Type\AbstractFilterType;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

/**
 * @extends FilterTypeInterface<DoctrineOrmProxyQueryInterface>
 */
abstract class AbstractDoctrineOrmFilterType extends AbstractFilterType
{
    public function apply(ProxyQueryInterface $query, FilterData $data, FilterInterface $filter, array $options): void
    {
        if (!$query instanceof DoctrineOrmProxyQueryInterface) {
            throw new UnexpectedTypeException($query, DoctrineOrmProxyQueryInterface::class);
        }

        $operator = $this->getFilterOperator($data, $filter);
        $value = $this->getFilterValue($data);

        if (!in_array($operator, $filter->getConfig()->getSupportedOperators())) {
            return;
        }

        $queryPath = $this->getFilterQueryPath($query, $filter);

        $parameterName = $this->getUniqueParameterName($query, $filter);

        try {
            $expression = $this->getOperatorExpression($queryPath, $parameterName, $operator, new Expr());
        } catch (InvalidArgumentException) {
            return;
        }

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

    public function getUniqueParameterName(DoctrineOrmProxyQueryInterface $query, FilterInterface $filter): string
    {
        return $filter->getFormName().'_'.$query->getUniqueParameterId();
    }

    protected function getParameterValue(Operator $operator, mixed $value): mixed
    {
        return $value;
    }

    protected function getFilterQueryPath(DoctrineOrmProxyQueryInterface $query, FilterInterface $filter): string
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
