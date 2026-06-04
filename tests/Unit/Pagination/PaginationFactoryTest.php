<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Pagination;

use Kreyu\Bundle\DataTableBundle\Pagination\CurrentPageOutOfRangeException;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationContext;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationFactory;
use PHPUnit\Framework\TestCase;

class PaginationFactoryTest extends TestCase
{
    public function testCreatesPaginationFromContext()
    {
        $pagination = (new PaginationFactory())->create(new PaginationContext(
            currentPageNumber: 10,
            currentPageItemCount: 10,
            totalItemCount: 250,
            itemNumberPerPage: 10,
            visiblePagesRange: 5,
        ));

        $this->assertSame(10, $pagination->getCurrentPageNumber());
        $this->assertSame(10, $pagination->getCurrentPageItemCount());
        $this->assertSame(250, $pagination->getTotalItemCount());
        $this->assertSame(10, $pagination->getItemNumberPerPage());
        $this->assertSame(5, $pagination->getFirstVisiblePageNumber());
        $this->assertSame(15, $pagination->getLastVisiblePageNumber());
    }

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
