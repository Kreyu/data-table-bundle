<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Event\DoctrineOrmFilterEvent;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Event\DoctrineOrmFilterEvents;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Event\PreApplyExpressionEvent;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Event\PreSetParametersEvent;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionFactory\ExpressionFactory;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionFactory\ExpressionFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ParameterFactory\ParameterFactory;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ParameterFactory\ParameterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterHandlerInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

class DoctrineOrmFilterHandler implements FilterHandlerInterface
{
    public function __construct(
        private readonly ExpressionFactoryInterface $expressionFactory = new ExpressionFactory(),
        private readonly ParameterFactoryInterface $parameterFactory = new ParameterFactory(),
    ) {
    }

    public function handle(ProxyQueryInterface $query, FilterData $data, FilterInterface $filter): void
    {
        if (!$query instanceof DoctrineOrmProxyQueryInterface) {
            throw new UnexpectedTypeException($query, DoctrineOrmProxyQueryInterface::class);
        }

        $parameters = $this->parameterFactory->create($query, $data, $filter);

        $event = new PreSetParametersEvent($query, $data, $filter, $parameters);

        $this->dispatch(DoctrineOrmFilterEvents::PRE_SET_PARAMETERS, $event);

        $queryBuilder = $query->getQueryBuilder();

        foreach ($event->getParameters() as $parameter) {
            $queryBuilder->setParameter(
                $parameter->getName(),
                $parameter->getValue(),
                $parameter->typeWasSpecified() ? $parameter->getType() : null,
            );
        }

        $expression = $this->expressionFactory->create($query, $data, $filter, $event->getParameters());

        $event = new PreApplyExpressionEvent($query, $data, $filter, $expression);

        $this->dispatch(DoctrineOrmFilterEvents::PRE_APPLY_EXPRESSION, $event);

        $queryBuilder->andWhere($event->getExpression());
    }

    private function dispatch(string $eventName, DoctrineOrmFilterEvent $event): void
    {
        $dispatcher = $event->getFilter()->getConfig()->getEventDispatcher();

        if ($dispatcher->hasListeners($eventName)) {
            $dispatcher->dispatch($event, $eventName);
        }
    }
}
