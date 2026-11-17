<?php
namespace Flowpack\Listable\Tests\Functional\Fusion;

use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\Tests\FunctionalTestCase;
use Neos\Fusion\Core\FusionGlobals;
use Neos\Fusion\Core\FusionSourceCodeCollection;
use Neos\Fusion\Core\RuntimeFactory;
use Psr\Http\Message\ServerRequestFactoryInterface;

/**
 * Base class for tests that evaluate this package's Fusion prototypes in a booted Flow application.
 *
 * Only the Fusion files given in {@see self::FUSION_FILES} are loaded (not the whole Neos.Neos Fusion), so tests
 * stay fast and independent of the content repository.
 */
abstract class AbstractFusionTestCase extends FunctionalTestCase
{
    protected const FUSION_FILES = [
        'resource://Flowpack.Listable/Private/Fusion/Pagination.fusion',
    ];

    /**
     * Evaluates the Fusion path $fusionPath, defined in $fusionSourceCode, with this package's Fusion loaded.
     */
    protected function evaluateFusion(string $fusionPath, string $fusionSourceCode): mixed
    {
        $sourceCode = FusionSourceCodeCollection::empty();
        foreach (static::FUSION_FILES as $fusionFile) {
            $sourceCode = $sourceCode->union(FusionSourceCodeCollection::fromFilePath($fusionFile));
        }
        $sourceCode = $sourceCode->union(FusionSourceCodeCollection::fromString($fusionSourceCode));

        $runtime = $this->objectManager->get(RuntimeFactory::class)
            ->createFromSourceCode($sourceCode, FusionGlobals::fromArray(['request' => $this->buildActionRequest()]));

        return $runtime->evaluate($fusionPath);
    }

    private function buildActionRequest(): ActionRequest
    {
        $httpRequest = $this->objectManager->get(ServerRequestFactoryInterface::class)
            ->createServerRequest('GET', 'http://localhost/');

        return ActionRequest::fromHttpRequest($httpRequest);
    }
}
