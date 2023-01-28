<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Pagination;

use Kreyu\Bundle\DataTableBundle\DataTableView;

class PaginationView
{
    public array $vars = [];

    public function __construct(
        private DataTableView $parent,
        PaginationInterface $pagination,
    ) {
        $this->vars = [
            'current_page_number' => $pagination->getCurrentPageNumber(),
            'total_item_count' => $pagination->getTotalItemCount(),
            'item_number_per_page' => $pagination->getItemNumberPerPage(),
            'page_count' => $pagination->getPageCount(),
            'has_previous_page' => $pagination->hasPreviousPage(),
            'has_next_page' => $pagination->hasNextPage(),
            'page_parameter_name' => $this->parent->vars['page_parameter_name'],
        ];
    }
}