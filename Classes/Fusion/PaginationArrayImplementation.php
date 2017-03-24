<?php
namespace Flowpack\Listable\Fusion;

use Neos\Flow\Annotations as Flow;
use Neos\Fusion\FusionObjects\AbstractFusionObject;

class PaginationArrayImplementation extends AbstractFusionObject
{
	/**
	 * @return Array
	 */
	public function evaluate()
	{
		$maximumNumberOfLinks = $this->tsValue('maximumNumberOfLinks') - 2;
		$itemsPerPage = $this->tsValue('itemsPerPage');
		$count = $this->tsValue('count');
		$currentPage = $this->tsValue('currentPage');
		if ($count > 0 !== true) {
			return [];
		}
		$numberOfPages = ceil($count / $itemsPerPage);
		if ($maximumNumberOfLinks > $numberOfPages) {
			$maximumNumberOfLinks = $numberOfPages;
		}
		$delta = floor($maximumNumberOfLinks / 2);
		$displayRangeStart = $currentPage - $delta;
		$displayRangeEnd = $currentPage + $delta + ($maximumNumberOfLinks % 2 === 0 ? 1 : 0);
		if ($displayRangeStart < 1) {
			$displayRangeEnd -= $displayRangeStart - 1;
		}
		if ($displayRangeEnd > $numberOfPages) {
			$displayRangeStart -= ($displayRangeEnd - $numberOfPages);
		}
		$displayRangeStart = (integer)max($displayRangeStart, 1);
		$displayRangeEnd = (integer)min($displayRangeEnd, $numberOfPages);
		$links = \range($displayRangeStart, $displayRangeEnd);
		if ($displayRangeStart > 2) {
			array_unshift($links, "...");
			array_unshift($links, 1);
		}
		if ($displayRangeEnd + 1 < $numberOfPages) {
			$links[] = "...";
			$links[] = $numberOfPages;
		}
		return $links;
	}
}
