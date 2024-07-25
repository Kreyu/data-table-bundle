<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Pagination;

use Kreyu\Bundle\DataTableBundle\DataTableView;

class PaginationView
{
    public array $vars = [];

    public function __construct(
        public DataTableView $parent,
        PaginationInterface $pagination,
    ) {
        $this->vars = [
            'pagination' => $this,
            'page_parameter_name' => $this->parent->vars['page_parameter_name'],
            'current_page_number' => $pagination->getCurrentPageNumber(),
            'current_page_item_count' => $pagination->getCurrentPageItemCount(),
            'total_item_count' => $pagination->getTotalItemCount(),
            'item_number_per_page' => $pagination->getItemNumberPerPage(),
            'page_count' => $pagination->getPageCount(),
            'has_previous_page' => $pagination->hasPreviousPage(),
            'has_next_page' => $pagination->hasNextPage(),
            'first_visible_page_number' => $pagination->getFirstVisiblePageNumber(),
            'last_visible_page_number' => $pagination->getLastVisiblePageNumber(),
            'current_page_first_item_index' => $pagination->getCurrentPageFirstItemIndex(),
            'current_page_last_item_index' => $pagination->getCurrentPageLastItemIndex(),
        ];
    }
}
