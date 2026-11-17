<?php
namespace Flowpack\Listable\Tests\Functional\Fusion;

use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\Mvc\ActionResponse;
use Neos\Flow\Mvc\Controller\Arguments;
use Neos\Flow\Mvc\Controller\ControllerContext;
use Neos\Flow\Mvc\Routing\UriBuilder;
use Neos\Flow\Tests\FunctionalTestCase;
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
     *
     * @return mixed
     */
    protected function evaluateFusion(string $fusionPath, string $fusionSourceCode)
    {
        $sourceCode = FusionSourceCodeCollection::empty();
        foreach (static::FUSION_FILES as $fusionFile) {
            $sourceCode = $sourceCode->union(FusionSourceCodeCollection::fromFilePath($fusionFile));
        }
        $sourceCode = $sourceCode->union(FusionSourceCodeCollection::fromString($fusionSourceCode));

        $runtime = $this->objectManager->get(RuntimeFactory::class)
            ->createFromSourceCode($sourceCode, $this->buildControllerContext());

        return $runtime->evaluate($fusionPath);
    }

    private function buildControllerContext(): ControllerContext
    {
        $httpRequest = $this->objectManager->get(ServerRequestFactoryInterface::class)
            ->createServerRequest('GET', 'http://localhost/');
        $request = ActionRequest::fromHttpRequest($httpRequest);

        $uriBuilder = new UriBuilder();
        $uriBuilder->setRequest($request);

        return new ControllerContext($request, new ActionResponse(), new Arguments([]), $uriBuilder);
    }
}
