<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmProxyQuery;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFilterType extends AbstractFilterType implements SearchFilterTypeInterface
{
    /**
     * @param DoctrineOrmProxyQuery $query
     */
    public function apply(ProxyQueryInterface $query, FilterData $data, FilterInterface $filter, array $options): void
    {
        $options['handler']($query, (string) $data->getValue(), $filter);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'field_type' => SearchType::class,
                'field_options' => [
                    'attr' => [
                        'placeholder' => 'Search...',
                    ],
                ],
                'label' => false,
                'operator_options' => [
                    'visible' => false,
                    'choices' => [],
                ],
            ])
            ->setRequired('handler')
            ->setAllowedTypes('handler', 'callable')
        ;
    }
}
