<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Extension\Core;

use Kreyu\Bundle\DataTableBundle\Extension\AbstractDataTableTypeExtension;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectNotFoundException;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectProviderInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DefaultConfigurationDataTableTypeExtension extends AbstractDataTableTypeExtension
{
    public function __construct(
        private array $defaults,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'themes' => $this->defaults['themes'],
            'column_factory' => $this->defaults['column_factory'],
            'action_factory' => $this->defaults['action_factory'],
            'request_handler' => $this->defaults['request_handler'],
            'sorting_enabled' => $this->defaults['sorting']['enabled'],
            'sorting_persistence_enabled' => $this->defaults['sorting']['persistence_enabled'],
            'sorting_persistence_adapter' => $this->defaults['sorting']['persistence_adapter'],
            'sorting_persistence_subject_provider' => $this->defaults['sorting']['persistence_subject_provider'],
            'pagination_enabled' => $this->defaults['pagination']['enabled'],
            'pagination_persistence_enabled' => $this->defaults['pagination']['persistence_enabled'],
            'pagination_persistence_adapter' => $this->defaults['pagination']['persistence_adapter'],
            'pagination_persistence_subject_provider' => $this->defaults['pagination']['persistence_subject_provider'],
            'filtration_enabled' => $this->defaults['filtration']['enabled'],
            'filtration_persistence_enabled' => $this->defaults['filtration']['persistence_enabled'],
            'filtration_persistence_adapter' => $this->defaults['filtration']['persistence_adapter'],
            'filtration_persistence_subject_provider' => $this->defaults['filtration']['persistence_subject_provider'],
            'filtration_form_factory' => $this->defaults['filtration']['form_factory'],
            'filter_factory' => $this->defaults['filtration']['filter_factory'],
            'personalization_enabled' => $this->defaults['personalization']['enabled'],
            'personalization_persistence_enabled' => $this->defaults['personalization']['persistence_enabled'],
            'personalization_persistence_adapter' => $this->defaults['personalization']['persistence_adapter'],
            'personalization_persistence_subject_provider' => $this->defaults['personalization']['persistence_subject_provider'],
            'personalization_form_factory' => $this->defaults['personalization']['form_factory'],
            'exporting_enabled' => $this->defaults['exporting']['enabled'],
            'exporting_form_factory' => $this->defaults['exporting']['form_factory'],
            'exporter_factory' => $this->defaults['exporting']['exporter_factory'],
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [DataTableType::class];
    }
}
