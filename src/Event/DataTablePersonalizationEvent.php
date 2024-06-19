<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Event;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;

class DataTablePersonalizationEvent extends DataTableEvent
{
    public function __construct(
        DataTableInterface          $dataTable,
        private PersonalizationData $data,
    ) {
        parent::__construct($dataTable);
    }

    /**
     * @deprecated use {@see getData()} instead
     */
    public function getPersonalizationData(): PersonalizationData
    {
        return $this->data;
    }

    /**
     * @deprecated use {@see getData()} instead
     */
    public function setPersonalizationData(PersonalizationData $personalizationData): void
    {
        $this->data = $personalizationData;
    }

    public function getData(): PersonalizationData
    {
        return $this->data;
    }

    public function setData(PersonalizationData $data): void
    {
        $this->data = $data;
    }
}
