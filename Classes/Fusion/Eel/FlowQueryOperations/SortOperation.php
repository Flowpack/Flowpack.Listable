<?php
namespace Flowpack\Listable\Fusion\Eel\FlowQueryOperations;

/*                                                                        *
 * This script belongs to the Flow package "Flowpack.Listable".           *
 * It was inspired by "Lelesys.News" package                              *
 *                                                                        */

use Neos\Eel\FlowQuery\FlowQueryException;
use Neos\Eel\FlowQuery\Operations\AbstractOperation;
use Neos\Eel\FlowQuery\FlowQuery;
use Neos\Flow\Annotations as Flow;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Utility\ObjectAccess;

/**
 * FlowQuery operation to sort Nodes by a property
 */
class SortOperation extends AbstractOperation
{
    /**
     * {@inheritdoc}
     */
    protected static $shortName = 'sort';

    /**
     * {@inheritdoc}
     */
    protected static $priority = 100;

    /**
     * {@inheritdoc}
     *
     * We can only handle CR Nodes.
     */
    public function canEvaluate($context)
    {
        return (!isset($context[0]) || ($context[0] instanceof NodeInterface));
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     * @throws FlowQueryException
     */
    public function evaluate(FlowQuery $flowQuery, array $arguments)
    {
        if (empty($arguments[0])) {
            throw new FlowQueryException('sort() needs property name by which nodes should be sorted', 1332492263);
        }

        $sortByPropertyPath = $arguments[0];
        $sortOrder = 'DESC';
        if (!empty($arguments[1]) && in_array($arguments[1], ['ASC', 'DESC'], true)) {
            $sortOrder = $arguments[1];
        }

        $sortedNodes = [];
        $sortSequence = [];
        $nodesByIdentifier = [];
        foreach ($flowQuery->getContext() as $node) {
            /** @var NodeInterface $node */
            $propertyValue = $sortByPropertyPath[0] === '_' ? ObjectAccess::getPropertyPath($node, substr($sortByPropertyPath, 1)) : $node->getProperty($sortByPropertyPath);
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
