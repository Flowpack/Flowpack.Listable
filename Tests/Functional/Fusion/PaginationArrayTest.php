<?php
namespace Flowpack\Listable\Tests\Functional\Fusion;

/**
 * Tests the page links computed by Flowpack.Listable:PaginationArray
 * (Flowpack\Listable\Fusion\PaginationArrayImplementation), evaluated through the Fusion runtime.
 */
class PaginationArrayTest extends AbstractFusionTestCase
{
    /**
     * @return array<string, array{int, int, int, int, bool, array<int|string>}>
     *   currentPage, numberOfPages, maximumNumberOfLinks, itemsPerPage, showPreviousNextLinks, expected links
     */
    public function paginationDataProvider(): array
    {
        return [
            'first page' => [1, 10, 5, 10, false, [1, 2, 3, '...', 10]],
            'middle page with gaps on both sides' => [5, 10, 5, 10, false, [1, '...', 4, 5, 6, '...', 10]],
            'last page' => [10, 10, 5, 10, false, [1, '...', 8, 9, 10]],
            'fewer pages than links' => [2, 3, 5, 10, false, [1, 2, 3]],
            'single page' => [1, 1, 5, 10, false, [1]],
            'previous and next links on a middle page' => [5, 10, 5, 10, true, ['previous', 1, '...', 4, 5, 6, '...', 10, 'next']],
            'no previous link on the first page' => [1, 10, 5, 10, true, [1, 2, 3, '...', 10, 'next']],
            'no next link on the last page' => [10, 10, 5, 10, true, ['previous', 1, '...', 8, 9, 10]],
        ];
    }

    /**
     * @test
     * @dataProvider paginationDataProvider
     * @param array<int|string> $expectedLinks
     */
    public function paginationArrayContainsExpectedLinks(
        int $currentPage,
        int $numberOfPages,
        int $maximumNumberOfLinks,
        int $itemsPerPage,
        bool $showPreviousNextLinks,
        array $expectedLinks
    ): void {
        $links = $this->evaluatePaginationArray(
            $currentPage,
            $numberOfPages * $itemsPerPage,
            $maximumNumberOfLinks,
            $itemsPerPage,
            $showPreviousNextLinks
        );

        // compared as strings, the way they are rendered: the last page is computed with ceil() and is a float
        self::assertSame(self::toStrings($expectedLinks), self::toStrings($links));
    }

    /**
     * @test
     */
    public function paginationArrayIsEmptyWithoutItems(): void
    {
        self::assertSame([], $this->evaluatePaginationArray(1, 0, 5, 10, false));
    }

    /**
     * @param array<int|float|string> $links
     * @return array<string>
     */
    private static function toStrings(array $links): array
    {
        return array_map(static fn ($link): string => (string)$link, $links);
    }

    /**
     * @return array<int|float|string>
     */
    private function evaluatePaginationArray(
        int $currentPage,
        int $totalCount,
        int $maximumNumberOfLinks,
        int $itemsPerPage,
        bool $showPreviousNextLinks
    ): array {
        return $this->evaluateFusion('pagination', sprintf(
            'pagination = Flowpack.Listable:PaginationArray {
                currentPage = %d
                totalCount = %d
                maximumNumberOfLinks = %d
                itemsPerPage = %d
                showPreviousNextLinks = %s
            }',
            $currentPage,
            $totalCount,
            $maximumNumberOfLinks,
            $itemsPerPage,
            $showPreviousNextLinks ? 'true' : 'false'
        ));
    }
}
