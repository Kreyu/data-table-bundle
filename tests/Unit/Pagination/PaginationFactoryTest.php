<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Pagination;

use Kreyu\Bundle\DataTableBundle\Pagination\CurrentPageOutOfRangeException;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationContext;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationFactory;
use PHPUnit\Framework\TestCase;

class PaginationFactoryTest extends TestCase
{
    public function testPropagatesCurrentPageOutOfRangeException()
    {
        $this->expectException(CurrentPageOutOfRangeException::class);

        (new PaginationFactory())->create(new PaginationContext(
            currentPageNumber: 2,
            currentPageItemCount: 10,
            totalItemCount: 10,
            itemNumberPerPage: 10,
            visiblePagesRange: 3,
        ));
    }
}
