<?php
namespace Flowpack\Listable\Fusion\Eel\FlowQueryOperations;

/*                                                                        *
 * This script belongs to the Flow package "Flowpack.Listable".           *
 *                                                                        */

use Neos\Flow\Annotations as Flow;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Eel\FlowQuery\FlowQuery;
use Neos\Eel\FlowQuery\Operations\AbstractOperation;

/**
 * Sort Nodes by their position in the node tree.
 *
 * Use it like this:
 *
 *    ${q(node).children().sortRecursive(['ASC'|'DESC'])}
 */
class SortRecursiveByIndexOperation extends AbstractOperation
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected static $shortName = 'sortRecursiveByIndex';

    /**
     * {@inheritdoc}
     *
     * @var int
     */
    protected static $priority = 100;

    /**
     * {@inheritdoc}
     *
     * We can only handle CR Nodes.
     *
     * @param mixed $context
     *
     * @return bool
     */
    public function canEvaluate($context)
    {
        return (isset($context[0]) && ($context[0] instanceof NodeInterface));
    }

    /**
     * {@inheritdoc}
     *
     * @param FlowQuery $flowQuery the FlowQuery object
     * @param array     $arguments the arguments for this operation
     *
     * @return mixed
     */
    public function evaluate(FlowQuery $flowQuery, array $arguments)
    {
        $nodes = $flowQuery->getContext();

        $sortOrder = 'ASC';
        if (isset($arguments[0]) && !empty($arguments[0]) && in_array($arguments[0], array('ASC', 'DESC'))) {
            $sortOrder = $arguments[0];
        }

        $indexPathCache = [];

        /** @var NodeInterface $node */
        foreach ($nodes as $node) {
            // Collect the list of sorting indices for all parents of the node and the node itself
            $nodeIdentifier = $node->getIdentifier();
            $indexPath = array($node->getIndex());
            while ($node = $node->getParent()) {
                $indexPath[] = $node->getIndex();
            }
            $indexPathCache[$nodeIdentifier] = $indexPath;
        }

        $cmpIndex = function (NodeInterface $a, NodeInterface $b) use ($indexPathCache) {
            if ($a == $b) {
                return 0;
            }

            // Compare index path starting from the site root until a difference is found
            $aIndexPath = $indexPathCache[$a->getIdentifier()];
            $bIndexPath = $indexPathCache[$b->getIdentifier()];
            while (count($aIndexPath) > 0 && count($bIndexPath) > 0) {
                $diff = (array_pop($aIndexPath) - array_pop($bIndexPath));
                if ($diff !== 0) {
                    return $diff < 0 ? -1 : 1;
                }
            }

            return 0;
        };

        usort($nodes, $cmpIndex);

        if ($sortOrder === 'DESC') {
            $nodes = array_reverse($nodes);
        }

        $flowQuery->setContext($nodes);
    }
}
