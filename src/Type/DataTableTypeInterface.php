<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Type;

use Kreyu\Bundle\DataTableBundle\Column\Mapper\ColumnMapperInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Mapper\FilterMapperInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Persistence\FilterPersisterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Persistence\FilterPersisterSubjectInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Persistence\FilterPersisterSubjectProviderInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface DataTableTypeInterface
{
    public function createQuery(): ProxyQueryInterface;

    public function configureOptions(OptionsResolver $resolver): void;

    public function configureColumns(ColumnMapperInterface $columns, array $options): void;

    public function configureFilters(FilterMapperInterface $filters, array $options): void;

    public function getFilterPersister(): ?FilterPersisterInterface;

    public function getFilterPersisterSubjectProvider(): ?FilterPersisterSubjectProviderInterface;

    public function hasPersonalization(): bool;

    public function getName(): string;
}
