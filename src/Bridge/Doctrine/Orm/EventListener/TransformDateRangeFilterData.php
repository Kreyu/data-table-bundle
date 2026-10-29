<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\EventListener;

use Kreyu\Bundle\DataTableBundle\Filter\Event\FilterEvents;
use Kreyu\Bundle\DataTableBundle\Filter\Event\PreHandleEvent;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TransformDateRangeFilterData implements EventSubscriberInterface
{
    public function preHandle(PreHandleEvent $event): void
    {
        $data = clone $event->getData();

        /** @var array{from?: \DateTimeInterface|null, to?: \DateTimeInterface|null}|null $value */
        $value = $data->getValue();

        $valueFrom = $this->createDate($value['from'] ?? null);
        $valueTo = $this->createDate($value['to'] ?? null);

        $valueFrom?->setTime(0, 0);

        $valueTo?->modify('+1 day');
        $valueTo?->setTime(0, 0);
        $valueTo?->modify('-1 microsecond');

        $this->applyRange($data, $valueFrom, $valueTo);

        $event->setData($data);
    }

    public static function getSubscribedEvents(): array
    {
        return [FilterEvents::PRE_HANDLE => 'preHandle'];
    }

    private function createDate(?\DateTimeInterface $date): ?\DateTime
    {
        if ($date === null) {
            return null;
        }

        return \DateTime::createFromInterface($date);
    }

    private function applyRange(FilterData $data, ?\DateTime $valueFrom, ?\DateTime $valueTo): void
    {
        if ($valueFrom === null) {
            if ($valueTo !== null) {
                $data->setValue($valueTo);
                $data->setOperator(Operator::LessThanEquals);
            }

            return;
        }

        if ($valueTo === null) {
            $data->setValue($valueFrom);
            $data->setOperator(Operator::GreaterThanEquals);

            return;
        }

        $data->setValue(['from' => $valueFrom, 'to' => $valueTo]);
        $data->setOperator(Operator::Between);
    }
}
