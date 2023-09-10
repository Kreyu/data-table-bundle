<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Persistence;

enum PersistenceContext: string
{
    case Sorting = 'sorting';
    case Pagination = 'pagination';
    case Filtration = 'filtration';
    case Personalization = 'personalization';
}
