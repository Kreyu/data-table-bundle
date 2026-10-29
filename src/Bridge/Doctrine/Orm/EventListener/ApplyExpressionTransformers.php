<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\EventListener;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Event\DoctrineOrmFilterEvents;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Event\PreApplyExpressionEvent;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionTransformer\ExpressionTransformerInterface;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionTransformer\LowerExpressionTransformer;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionTransformer\TrimExpressionTransformer;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionTransformer\UpperExpressionTransformer;
use Kreyu\Bundle\DataTableBundle\Filter\FilterConfigInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ApplyExpressionTransformers implements EventSubscriberInterface
{
    public function preApplyExpression(PreApplyExpressionEvent $event): void
    {
        $expressionTransformers = $this->getExpressionTransformers($event->getFilter()->getConfig());

        $event->setExpression(array_reduce(
            $expressionTransformers,
            static fn (
                mixed $expression,
                ExpressionTransformerInterface $expressionTransformer,
            ): mixed => $expressionTransformer->transform($expression),
            $event->getExpression(),
        ));
    }

    public static function getSubscribedEvents(): array
    {
        return [DoctrineOrmFilterEvents::PRE_APPLY_EXPRESSION => 'preApplyExpression'];
    }

    /**
     * @return array<ExpressionTransformerInterface>
     */
    private function getExpressionTransformers(FilterConfigInterface $config): array
    {
        /** @var bool $trim */
        $trim = $config->getOption('trim');

        /** @var bool $lower */
        $lower = $config->getOption('lower');

        /** @var bool $upper */
        $upper = $config->getOption('upper');

        /** @var array<ExpressionTransformerInterface> $customExpressionTransformers */
        $customExpressionTransformers = $config->getOption('expression_transformers');

        $expressionTransformers = [];

        if ($trim) {
            $expressionTransformers[] = new TrimExpressionTransformer();
        }

        if ($lower) {
            $expressionTransformers[] = new LowerExpressionTransformer();
        }

        if ($upper) {
            $expressionTransformers[] = new UpperExpressionTransformer();
        }

        return [...$expressionTransformers, ...$customExpressionTransformers];
    }
}
