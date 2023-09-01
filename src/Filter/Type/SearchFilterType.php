<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmProxyQuery;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\OptionsResolver\Options;
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
                'form_type' => SearchType::class,
                'label' => false,
            ])
            ->setRequired('handler')
            ->setAllowedTypes('handler', 'callable')
            ->addNormalizer('form_options', function (Options $options, array $value): array {
                return $value + [
                    'attr' => ($value['attr'] ?? []) + ['placeholder' => 'Search...'],
                ];
            })
        ;
    }
}
