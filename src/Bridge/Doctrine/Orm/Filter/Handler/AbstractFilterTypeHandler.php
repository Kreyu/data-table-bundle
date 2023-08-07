<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Handler;

use Doctrine\ORM\QueryBuilder;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmProxyQuery;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Handler\FilterTypeHandlerInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Doctrine\ORM\Query\Expr;

/**
 * @template-extends FilterTypeHandlerInterface<DoctrineOrmProxyQuery>
 */
abstract class AbstractFilterTypeHandler implements FilterTypeHandlerInterface
{
    public function handle(ProxyQueryInterface $query, FilterData $data, FilterInterface $filter): void
    {
        $operator = $data->getOperator();
        $value = $data->getValue();

        if (null === $operator || null === $value) {
            return;
        }

        // Property path expression, e.g. "product.name"
        $propertyPathExpression = $this->getPropertyPathExpression($query, $filter);

        // Value expression, e.g. "%foo%"
        $valueExpression = $this->getValueExpression($operator, $value);

        // Unique parameter name, e.g. "product_text_1"
        $parameterName = $this->getUniqueParameterName($query, $filter);

        $query
            ->andWhere($this->getComparisonExpression($propertyPathExpression, ":$parameterName"))
            ->setParameter($parameterName, $valueExpression($operator, $value));
    }

    public function supports(ProxyQueryInterface $query, FilterData $data): bool
    {
        return $query instanceof QueryBuilder;
    }

    protected function getUniqueParameterName(DoctrineOrmProxyQuery $query, FilterInterface $filter): string
    {
        return $filter->getFormName().'_'.$query->getUniqueParameterId();
    }

    protected function getPropertyPathExpression(DoctrineOrmProxyQuery $query, FilterInterface $filter): string
    {
        $rootAlias = current($query->getRootAliases());

        $queryPath = $filter->getQueryPath();

        if ($rootAlias && !str_contains($queryPath, '.') && $filter->getOption('auto_alias_resolving')) {
            $queryPath = $rootAlias.'.'.$queryPath;
        }

        return $queryPath;
    }

    abstract protected function getValueExpression(Operator $operator, mixed $value): string;

    abstract protected function getComparisonExpression(Operator $operator, string $x, string $y): Expr\Comparison;
}
