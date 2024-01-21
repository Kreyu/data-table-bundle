<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\EventListener;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Event\DoctrineOrmFilterEvents;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Event\PreApplyExpressionEvent;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionTransformer\LowerExpressionTransformer;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionTransformer\TrimExpressionTransformer;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionTransformer\UpperExpressionTransformer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ApplyExpressionTransformers implements EventSubscriberInterface
{
    public function preApplyExpression(PreApplyExpressionEvent $event): void
    {
        $filter = $event->getFilter();
        $expression = $event->getExpression();

        if ($filter->getConfig()->getOption('trim')) {
            $expression = (new TrimExpressionTransformer())->transform($expression);
        }

        if ($filter->getConfig()->getOption('lower')) {
            $expression = (new LowerExpressionTransformer())->transform($expression);
        }

        if ($filter->getConfig()->getOption('upper')) {
            $expression = (new UpperExpressionTransformer())->transform($expression);
        }

        foreach ($filter->getConfig()->getOption('expression_transformers') as $expressionTransformer) {
            $expression = $expressionTransformer->transform($expression);
        }

        $event->setExpression($expression);
    }

    public static function getSubscribedEvents(): array
    {
        return [DoctrineOrmFilterEvents::PRE_APPLY_EXPRESSION => 'preApplyExpression'];
    }
}
