<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @deprecated since 0.15, use {@see \Kreyu\Bundle\DataTableBundle\Filter\Type\CallbackFilterType} instead
 */
class CallbackFilterType extends AbstractDoctrineOrmFilterType
{
    public function apply(ProxyQueryInterface $query, FilterData $data, FilterInterface $filter, array $options): void
    {
        if (!$query instanceof DoctrineOrmProxyQueryInterface) {
            throw new UnexpectedTypeException($query, DoctrineOrmProxyQueryInterface::class);
        }

        $options['callback']($query, $data, $filter);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('supported_operators', Operator::cases())
            ->setRequired('callback')
            ->setAllowedTypes('callback', [\Closure::class])
        ;
    }
}
