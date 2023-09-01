<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Event;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;

class DataTablePersonalizationEvent extends DataTableEvent
{
    public function __construct(
        DataTableInterface $dataTable,
        private PersonalizationData $personalizationData,
    ) {
        parent::__construct($dataTable);
    }

    public function getPersonalizationData(): PersonalizationData
    {
        return $this->personalizationData;
    }

    public function setPersonalizationData(PersonalizationData $personalizationData): void
    {
        $this->personalizationData = $personalizationData;
    }
}
