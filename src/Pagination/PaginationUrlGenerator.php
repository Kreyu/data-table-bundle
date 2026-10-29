<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Pagination;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Request\AbstractUrlGenerator;

class PaginationUrlGenerator extends AbstractUrlGenerator implements PaginationUrlGeneratorInterface
{
    public function generate(DataTableView $dataTableView, int $page): string
    {
        $parameters = $this->getUrlParameters($dataTableView);

        /** @var string $pageParameterName */
        $pageParameterName = $dataTableView->vars['page_parameter_name'];

        $parameters[$pageParameterName] = $page;

        return $this->generateUrl($parameters);
    }
}
