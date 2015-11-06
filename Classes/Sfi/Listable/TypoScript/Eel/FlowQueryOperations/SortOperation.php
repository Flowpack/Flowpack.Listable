<?php
namespace Sfi\Listable\TypoScript\Eel\FlowQueryOperations;

/*                                                                        *
 * This script was borrowed from package "Lelesys.News".		          *
 *                                                                        *
 *                                                                        */

use TYPO3\Eel\FlowQuery\Operations\AbstractOperation;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;
use TYPO3\Eel\FlowQuery\FlowQuery;
use TYPO3\TYPO3CR\Domain\Model\Node;

/**
 * EEL sort() operation to sort Nodes
 */
class SortOperation extends AbstractOperation {
	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	static protected $shortName = 'sort';

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
	 * @param array $arguments the arguments for this operation
	 * @return mixed
	 */
	public function evaluate(FlowQuery $flowQuery, array $arguments) {
		if (!isset($arguments[0]) || empty($arguments[0])) {
			//throw new \TYPO3\Eel\FlowQuery\FlowQueryException('sort() needs property name by which nodes should be sorted', 1332492263);
		} else {
			$nodes = $flowQuery->getContext();
			$sortByPropertyPath = $arguments[0];
			$sortOrder = 'DESC';
			if (isset($arguments[1]) && !empty($arguments[1]) && in_array($arguments[1], array('ASC', 'DESC'))) {
				$sortOrder = $arguments[1];
			}

			$sortedNodes = array();
			$sortSequence = array();
			$nodesByIdentifier = array();
			/** @var Node $node  */
			foreach ($nodes as $node) {
				if ($sortByPropertyPath[0] === '_') {
					$propertyValue = \TYPO3\Flow\Reflection\ObjectAccess::getPropertyPath($node, substr($sortByPropertyPath, 1));
				} else {
					$propertyValue = $node->getProperty($sortByPropertyPath);
				}

				if ($propertyValue instanceof \DateTime) {
					$propertyValue = $propertyValue->getTimestamp();
				}
				$sortSequence[$node->getIdentifier()] = $propertyValue;
				$nodesByIdentifier[$node->getIdentifier()] = $node;
			}
			if ($sortOrder === 'DESC') {
				arsort($sortSequence);
			} else {
				asort($sortSequence);
			}
			foreach ($sortSequence as $nodeIdentifier => $value) {
				$sortedNodes[] = $nodesByIdentifier[$nodeIdentifier];
			}
			$flowQuery->setContext($sortedNodes);
		}
	}
}