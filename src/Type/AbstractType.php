<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Type;

use Kreyu\Bundle\DataTableBundle\Column\Mapper\ColumnMapperInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Mapper\FilterMapperInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Persistence\FilterPersisterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Persistence\FilterPersisterSubjectInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Persistence\FilterPersisterSubjectProviderInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\Persistence\PersonalizationPersisterInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\Persistence\PersonalizationPersisterSubjectInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\Persistence\PersonalizationPersisterSubjectProviderInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Symfony\Component\Form\Util\StringUtil;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractType implements DataTableTypeInterface
{
    private null|FilterPersisterInterface $filterPersister = null;
    private null|FilterPersisterSubjectProviderInterface $filterPersisterSubjectProvider = null;
    private null|PersonalizationPersisterInterface $personalizationPersister = null;
    private null|PersonalizationPersisterSubjectProviderInterface $personalizationPersisterSubjectProvider = null;

    public function configureOptions(OptionsResolver $resolver): void
    {
    }

    public function configureColumns(ColumnMapperInterface $columns, array $options): void
    {
    }

    public function configureFilters(FilterMapperInterface $filters, array $options): void
    {
    }

    public function getFilterPersister(): ?FilterPersisterInterface
    {
        return $this->filterPersister;
    }

    public function setFilterPersister(?FilterPersisterInterface $filterPersister = null): void
    {
        $this->filterPersister = $filterPersister;
    }

    public function getFilterPersisterSubjectProvider(): ?FilterPersisterSubjectProviderInterface
    {
        return $this->filterPersisterSubjectProvider;
    }

    public function setFilterPersisterSubjectProvider(?FilterPersisterSubjectProviderInterface $filterPersisterSubjectProvider = null): void
    {
        $this->filterPersisterSubjectProvider = $filterPersisterSubjectProvider;
    }

    public function getFilterPersisterSubject(): ?FilterPersisterSubjectInterface
    {
        return $this->getFilterPersisterSubjectProvider()?->provide();
    }

    public function getPersonalizationPersister(): ?PersonalizationPersisterInterface
    {
        return $this->personalizationPersister;
    }

    public function setPersonalizationPersister(?PersonalizationPersisterInterface $personalizationPersister = null): void
    {
        $this->personalizationPersister = $personalizationPersister;
    }

    public function getPersonalizationPersisterSubjectProvider(): ?PersonalizationPersisterSubjectProviderInterface
    {
        return $this->personalizationPersisterSubjectProvider;
    }

    public function setPersonalizationPersisterSubjectProvider(?PersonalizationPersisterSubjectProviderInterface $personalizationPersisterSubjectProvider = null): void
    {
        $this->personalizationPersisterSubjectProvider = $personalizationPersisterSubjectProvider;
    }

    public function getPersonalizationPersisterSubject(): ?PersonalizationPersisterSubjectInterface
    {
        return $this->getPersonalizationPersisterSubjectProvider()?->provide();
    }

    public function hasPersonalizationEnabled(): bool
    {
        return true;
    }

    public function getName(): string
    {
        return StringUtil::fqcnToBlockPrefix(static::class);
    }
}
