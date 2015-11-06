<?php
namespace Sfi\Listable\TypoScript\Eel\FlowQueryOperations;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Sfi.Listable".          *
 *                                                                        *
 *                                                                        */

use TYPO3\Eel\FlowQuery\FlowQuery;
use TYPO3\Eel\FlowQuery\Operations\AbstractOperation;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\TYPO3CR\Domain\Model\Node;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

/**
 * FlowQuery operation to filter by properties of type reference or references
 */
class FilterByReferenceOperation extends AbstractOperation {
	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	static protected $shortName = 'filterByReference';

	/**
	 * {@inheritdoc}
	 *
	 * @var integer
	 */
	static protected $priority = 100;

	/**
	 * {@inheritdoc}
	 *
	 * We can only handle TYPO3CR Nodes.
	 *
	 * @param mixed $context
	 * @return boolean
	 */
	public function canEvaluate($context) {
		return (isset($context[0]) && ($context[0] instanceof NodeInterface));
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param FlowQuery $flowQuery the FlowQuery object
	 * @param array $arguments the arguments for this operation.
	 * First argument is property to filter by, must be of reference of references type. 
	 * Second is object to filter by, must be Node. 
	 * @return mixed
	 */
	public function evaluate(FlowQuery $flowQuery, array $arguments) {
		if (!isset($arguments[0]) || empty($arguments[0])) {
			throw new \TYPO3\Eel\FlowQuery\FlowQueryException('FilterByReference() needs reference property name by which nodes should be filtered', 1332492263);
		} else if (!isset($arguments[1]) || empty($arguments[1])) {
			throw new \TYPO3\Eel\FlowQuery\FlowQueryException('FilterByReference() needs object by which nodes should be filtered', 1332493263);
		} else {
			$nodes = $flowQuery->getContext();
			$filterByPropertyPath = $arguments[0];
			/** @var Node $object  */
			$object = $arguments[1];
			$filteredNodes = array();
			/** @var Node $node  */
			foreach ($nodes as $node) {
				$propertyValue = $node->getProperty($filterByPropertyPath);
				if (is_array($propertyValue)){
					if (in_array($object, $propertyValue)) {
						$filteredNodes[] = $node;
					}
				} else {
					if ($object == $propertyValue) {
						$filteredNodes[] = $node;
					}
				}
			}
			$flowQuery->setContext($filteredNodes);
		}
	}
}