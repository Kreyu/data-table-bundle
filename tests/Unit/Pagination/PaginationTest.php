<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Pagination;

use Kreyu\Bundle\DataTableBundle\Pagination\CurrentPageOutOfRangeException;
use Kreyu\Bundle\DataTableBundle\Pagination\Pagination;
use PHPUnit\Framework\TestCase;

class PaginationTest extends TestCase
{
    public function testCurrentPageOutOfRange()
    {
        $this->expectException(CurrentPageOutOfRangeException::class);

        new Pagination(
            currentPageNumber: 2,
            currentPageItemCount: 10,
            totalItemCount: 10,
        );
    }

    public function testGetPageCountWithItemNumber()
    {
        $pagination = new Pagination(
            currentPageNumber: 1,
            currentPageItemCount: 10,
            totalItemCount: 50,
            itemNumberPerPage: 10,
        );

        $this->assertSame(5, $pagination->getPageCount());
    }

    public function testGetPageCountWithItemNumberPerPageLessThanOne()
    {
        $pagination = new Pagination(
            currentPageNumber: 1,
            currentPageItemCount: 10,
            totalItemCount: 10,
            itemNumberPerPage: 0,
        );

        $this->assertSame(1, $pagination->getPageCount());
    }

    public function testHasPreviousPage()
    {
        $pagination = new Pagination(
            currentPageNumber: 2,
            currentPageItemCount: 10,
            totalItemCount: 50,
            itemNumberPerPage: 10,
        );

        $this->assertTrue($pagination->hasPreviousPage());

        $pagination = new Pagination(
            currentPageNumber: 1,
            currentPageItemCount: 10,
            totalItemCount: 50,
            itemNumberPerPage: 10,
        );

        $this->assertFalse($pagination->hasPreviousPage());
    }

    public function testHasNextPage()
    {
        $pagination = new Pagination(
            currentPageNumber: 4,
            currentPageItemCount: 10,
            totalItemCount: 50,
            itemNumberPerPage: 10,
        );

        $this->assertTrue($pagination->hasNextPage());

        $pagination = new Pagination(
            currentPageNumber: 5,
            currentPageItemCount: 10,
            totalItemCount: 50,
            itemNumberPerPage: 10,
        );

        $this->assertFalse($pagination->hasNextPage());
    }

    public function testGetFirstVisiblePageNumber()
    {
        $pagination = new Pagination(
            currentPageNumber: 10,
            currentPageItemCount: 10,
            totalItemCount: 250,
            itemNumberPerPage: 10,
        );

        $this->assertSame(7, $pagination->getFirstVisiblePageNumber());

        $pagination = new Pagination(
            currentPageNumber: 1,
            currentPageItemCount: 10,
            totalItemCount: 250,
            itemNumberPerPage: 10,
        );

        $this->assertSame(1, $pagination->getFirstVisiblePageNumber());
    }

    public function testGetLastVisiblePageNumber()
    {
        $pagination = new Pagination(
            currentPageNumber: 10,
            currentPageItemCount: 10,
            totalItemCount: 250,
            itemNumberPerPage: 10,
        );

        $this->assertSame(13, $pagination->getLastVisiblePageNumber());

        $pagination = new Pagination(
            currentPageNumber: 25,
            currentPageItemCount: 10,
            totalItemCount: 250,
            itemNumberPerPage: 10,
        );

        $this->assertSame(25, $pagination->getLastVisiblePageNumber());
    }

    public function testGetCurrentPageFirstItemIndex()
    {
        $pagination = new Pagination(
            currentPageNumber: 10,
            currentPageItemCount: 10,
            totalItemCount: 250,
            itemNumberPerPage: 10,
        );

        $this->assertSame(91, $pagination->getCurrentPageFirstItemIndex());
    }

    public function testGetCurrentPageLastItemIndex()
    {
        $pagination = new Pagination(
            currentPageNumber: 10,
            currentPageItemCount: 10,
            totalItemCount: 250,
            itemNumberPerPage: 10,
        );

        $this->assertSame(100, $pagination->getCurrentPageLastItemIndex());
    }
}