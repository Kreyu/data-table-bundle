<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Symfony\Component\Form\FormInterface;

interface DataTableInterface
{
    public function getConfig(): DataTableConfigInterface;

    public function sort(SortingData $sortingData): void;

    public function filter(FiltrationData $filtrationData): void;

    public function paginate(PaginationData $paginationData): void;

    public function personalize(PersonalizationData $personalizationData): void;

    public function handleRequest(mixed $request): void;

    public function getPagination(): PaginationInterface;

    public function getFiltrationForm(): FormInterface;

    public function getPersonalizationForm(): FormInterface;

    public function createView(): DataTableView;
}
