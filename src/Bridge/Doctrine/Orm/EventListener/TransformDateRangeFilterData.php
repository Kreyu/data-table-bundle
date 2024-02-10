<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\EventListener;

use Kreyu\Bundle\DataTableBundle\Filter\Event\FilterEvents;
use Kreyu\Bundle\DataTableBundle\Filter\Event\PreHandleEvent;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TransformDateRangeFilterData implements EventSubscriberInterface
{
    public function preHandle(PreHandleEvent $event): void
    {
        $data = $event->getData();
        $value = $data->getValue();

        $valueFrom = $value['from'] ?? null;
        $valueTo = $value['to'] ?? null;

        if ($valueFrom) {
            $valueFrom = \DateTime::createFromInterface($valueFrom);
            $valueFrom->setTime(0, 0);
        }

        if ($valueTo) {
            $valueTo = \DateTime::createFromInterface($valueTo)->modify('+1 day');
            $valueTo->setTime(0, 0);
        }

        $data = clone $data;

        if ($valueFrom && $valueTo) {
            $data->setValue(['from' => $valueFrom, 'to' => $valueTo]);
            $data->setOperator(Operator::Between);
        } elseif ($valueFrom) {
            $data->setValue($valueFrom);
            $data->setOperator(Operator::GreaterThanEquals);
        } elseif ($valueTo) {
            $data->setValue($valueTo);
            $data->setOperator(Operator::LessThan);
        }

        $event->setData($data);
    }

    public static function getSubscribedEvents(): array
    {
        return [FilterEvents::PRE_HANDLE => 'preHandle'];
    }
}
