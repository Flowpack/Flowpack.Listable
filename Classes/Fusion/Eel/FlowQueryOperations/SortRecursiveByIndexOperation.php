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
     */
    protected static $shortName = 'sortRecursiveByIndex';

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
        return (isset($context[0]) && ($context[0] instanceof NodeInterface));
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function evaluate(FlowQuery $flowQuery, array $arguments)
    {
        $sortOrder = 'ASC';
        if (!empty($arguments[0]) && in_array($arguments[0], ['ASC', 'DESC'], true)) {
            $sortOrder = $arguments[0];
        }

        $indexPathCache = [];

        /** @var NodeInterface $node */
        foreach ($flowQuery->getContext() as $node) {
            // Collect the list of sorting indices for all parents of the node and the node itself
            $nodeIdentifier = $node->getIdentifier();
            $indexPath = [$node->getIndex()];
            while ($node = $node->getParent()) {
                $indexPath[] = $node->getIndex();
            }
            $indexPathCache[$nodeIdentifier] = $indexPath;
        }

        $flip = $sortOrder === 'DESC' ? -1 : 1;

        usort($nodes, function (NodeInterface $a, NodeInterface $b) use ($indexPathCache, $flip) {
            if ($a === $b) {
                return 0;
            }

            // Compare index path starting from the site root until a difference is found
            $aIndexPath = $indexPathCache[$a->getIdentifier()];
            $bIndexPath = $indexPathCache[$b->getIdentifier()];
            while (count($aIndexPath) > 0 && count($bIndexPath) > 0) {
                $diff = (array_pop($aIndexPath) - array_pop($bIndexPath));
                if ($diff !== 0) {
                    return $flip * $diff < 0 ? -1 : 1;
                }
            }

            return 0;
        });

        $flowQuery->setContext($nodes);
    }
}
