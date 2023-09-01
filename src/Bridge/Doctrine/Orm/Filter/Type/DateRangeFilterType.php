<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmProxyQuery;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Form\Type\DateRangeType;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;

use function Symfony\Component\Translation\t;

class DateRangeFilterType extends AbstractFilterType
{
    /**
     * @param DoctrineOrmProxyQuery $query
     */
    public function apply(ProxyQueryInterface $query, FilterData $data, FilterInterface $filter, array $options): void
    {
        $value = $data->getValue();

        $parameterName = $this->getUniqueParameterName($query, $filter);

        $queryPath = $this->getFilterQueryPath($query, $filter);

        $criteria = $query->expr()->andX();

        if (!is_array($value)) {
            return;
        }

        if (null !== $dateFrom = $value['from'] ?? null) {
            $parameterNameFrom = $parameterName.'_from';

            $dateFrom = \DateTime::createFromInterface($dateFrom);
            $dateFrom->setTime(0, 0);

            $criteria->add($query->expr()->gte($queryPath, ":$parameterNameFrom"));

            $query->setParameter($parameterNameFrom, $dateFrom);
        }

        if (null !== $valueTo = $value['to'] ?? null) {
            $parameterNameTo = $parameterName.'_to';

            $valueTo = \DateTime::createFromInterface($valueTo)->modify('+1 day');
            $valueTo->setTime(0, 0);

            $criteria->add($query->expr()->lt($queryPath, ":$parameterNameTo"));

            $query->setParameter($parameterNameTo, $valueTo);
        }

        if ($criteria->count() > 0) {
            $query->andWhere($criteria);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'form_type' => DateRangeType::class,
                'active_filter_formatter' => $this->getFormattedActiveFilterString(...),
                'empty_data' => ['from' => '', 'to' => ''],
            ])
        ;
    }

    public function getFormattedActiveFilterString(FilterData $data): string|TranslatableMessage
    {
        $value = $data->getValue();

        $dateFrom = $value['from'];
        $dateTo = $value['to'];

        if (null !== $dateFrom && null === $dateTo) {
            return t('After %date%', ['%date%' => $dateFrom->format('Y-m-d')], 'KreyuDataTable');
        }

        if (null === $dateFrom && null !== $dateTo) {
            return t('Before %date%', ['%date%' => $dateTo->format('Y-m-d')], 'KreyuDataTable');
        }

        if ($dateFrom == $dateTo) {
            return $dateFrom->format('Y-m-d');
        }

        return $dateFrom->format('Y-m-d').' - '.$dateTo->format('Y-m-d');
    }
}
