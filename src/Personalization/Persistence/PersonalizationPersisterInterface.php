<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Personalization\Persistence;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;

interface PersonalizationPersisterInterface
{
    public function get(PersonalizationPersisterSubjectInterface $subject, DataTableInterface $dataTable): PersonalizationData;

    public function save(PersonalizationPersisterSubjectInterface $subject, DataTableInterface $dataTable, PersonalizationData $personalizationData): void;
}
