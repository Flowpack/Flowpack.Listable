<?php
namespace Flowpack\Listable\Tests\Functional\Fusion;

use PHPUnit\Framework\Attributes\Test;

/**
 * Tests the page links computed by Flowpack.Listable:PaginationArray
 * (Flowpack\Listable\Fusion\PaginationArrayImplementation), evaluated through the Fusion runtime.
 */
class PaginationArrayTest extends AbstractFusionTestCase
{
    #[Test]
    public function firstPage(): void
    {
        $this->assertPaginationLinks(
            // settings
            itemsPerPage: 10,
            maximumNumberOfLinks: 5,
            showPreviousNextLinks: false,

            // state
            totalCount: 100,
            currentPage: 1,

            // expected
            expectedLinks: [1, 2, 3, '...', 10]
        );
    }

    #[Test]
    public function middlePageWithGapsOnBothSides(): void
    {
        $this->assertPaginationLinks(
            // settings
            itemsPerPage: 10,
            maximumNumberOfLinks: 5,
            showPreviousNextLinks: false,

            // state
            totalCount: 100,
            currentPage: 5,

            // expected
            expectedLinks: [1, '...', 4, 5, 6, '...', 10]
        );
    }

    #[Test]
    public function firstPageIsLinkedWithoutSeparatorWhenTheRangeStartsAtPageTwo(): void
    {
        $this->assertPaginationLinks(
            // settings
            itemsPerPage: 10,
            maximumNumberOfLinks: 5,
            showPreviousNextLinks: false,

            // state
            totalCount: 100,
            currentPage: 3,

            // expected
            expectedLinks: [1, 2, 3, 4, '...', 10]
        );
    }

    #[Test]
    public function lastPageIsLinkedWithoutSeparatorWhenTheRangeEndsAtTheSecondToLastPage(): void
    {
        $this->assertPaginationLinks(
            // settings
            itemsPerPage: 10,
            maximumNumberOfLinks: 5,
            showPreviousNextLinks: false,

            // state
            totalCount: 100,
            currentPage: 8,

            // expected
            expectedLinks: [1, '...', 7, 8, 9, 10]
        );
    }

    #[Test]
    public function lastPage(): void
    {
        $this->assertPaginationLinks(
            // settings
            itemsPerPage: 10,
            maximumNumberOfLinks: 5,
            showPreviousNextLinks: false,

            // state
            totalCount: 100,
            currentPage: 10,

            // expected
            expectedLinks: [1, '...', 8, 9, 10]
        );
    }

    #[Test]
    public function partiallyFilledLastPageCountsAsPage(): void
    {
        $this->assertPaginationLinks(
            // settings
            itemsPerPage: 10,
            maximumNumberOfLinks: 5,
            showPreviousNextLinks: false,

            // state
            totalCount: 95,
            currentPage: 1,

            // expected
            expectedLinks: [1, 2, 3, '...', 10]
        );
    }

    #[Test]
    public function fewerPagesThanLinks(): void
    {
        $this->assertPaginationLinks(
            // settings
            itemsPerPage: 10,
            maximumNumberOfLinks: 5,
            showPreviousNextLinks: false,

            // state
            totalCount: 30,
            currentPage: 2,

            // expected
            expectedLinks: [1, 2, 3]
        );
    }

    #[Test]
    public function singlePage(): void
    {
        $this->assertPaginationLinks(
            // settings
            itemsPerPage: 10,
            maximumNumberOfLinks: 5,
            showPreviousNextLinks: false,

            // state
            totalCount: 10,
            currentPage: 1,

            // expected
            expectedLinks: [1]
        );
    }

    #[Test]
    public function noLinksWithoutItems(): void
    {
        $this->assertPaginationLinks(
            // settings
            itemsPerPage: 10,
            maximumNumberOfLinks: 5,
            showPreviousNextLinks: false,

            // state
            totalCount: 0,
            currentPage: 1,

            // expected
            expectedLinks: []
        );
    }

    #[Test]
    public function previousAndNextLinksOnAMiddlePage(): void
    {
        $this->assertPaginationLinks(
            // settings
            itemsPerPage: 10,
            maximumNumberOfLinks: 5,
            showPreviousNextLinks: true,

            // state
            totalCount: 100,
            currentPage: 5,

            // expected
            expectedLinks: ['previous', 1, '...', 4, 5, 6, '...', 10, 'next']
        );
    }

    #[Test]
    public function previousLinkStaysInFrontOfTheFirstPageLink(): void
    {
        $this->assertPaginationLinks(
            // settings
            itemsPerPage: 10,
            maximumNumberOfLinks: 5,
            showPreviousNextLinks: true,

            // state
            totalCount: 100,
            currentPage: 3,

            // expected
            expectedLinks: ['previous', 1, 2, 3, 4, '...', 10, 'next']
        );
    }

    #[Test]
    public function noPreviousLinkOnTheFirstPage(): void
    {
        $this->assertPaginationLinks(
            // settings
            itemsPerPage: 10,
            maximumNumberOfLinks: 5,
            showPreviousNextLinks: true,

            // state
            totalCount: 100,
            currentPage: 1,

            // expected
            expectedLinks: [1, 2, 3, '...', 10, 'next']
        );
    }

    #[Test]
    public function noNextLinkOnTheLastPage(): void
    {
        $this->assertPaginationLinks(
            // settings
            itemsPerPage: 10,
            maximumNumberOfLinks: 5,
            showPreviousNextLinks: true,

            // state
            totalCount: 100,
            currentPage: 10,

            // expected
            expectedLinks: ['previous', 1, '...', 8, 9, 10]
        );
    }

    /**
     * @param array<int|string> $expectedLinks
     */
    private function assertPaginationLinks(
        // settings
        int $itemsPerPage,
        int $maximumNumberOfLinks,
        bool $showPreviousNextLinks,
        // state
        int $totalCount,
        int $currentPage,
        // expected
        array $expectedLinks
    ): void {
        $links = $this->evaluateFusion('pagination', sprintf(
            'pagination = Flowpack.Listable:PaginationArray {
                currentPage = %d
                totalCount = %d
                itemsPerPage = %d
                maximumNumberOfLinks = %d
                showPreviousNextLinks = %s
            }',
            $currentPage,
            $totalCount,
            $itemsPerPage,
            $maximumNumberOfLinks,
            $showPreviousNextLinks ? 'true' : 'false'
        ));

        self::assertSame($expectedLinks, $links);
    }
}
