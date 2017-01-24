<?php
namespace Flowpack\Listable\Tests\Functional\FusionObjects;

use Neos\Flow\Http\Request;
use Neos\Flow\Http\Response;
use Neos\Flow\Mvc\Controller\Arguments;
use Neos\Flow\Mvc\Controller\ControllerContext;
use Neos\Flow\Mvc\Routing\UriBuilder;
use Neos\Flow\Tests\FunctionalTestCase;
use Neos\Fusion\View\FusionView;

abstract class AbstractTypoScriptObjectTest extends FunctionalTestCase
{
    /**
     * @var ControllerContext
     */
    protected $controllerContext;

    /**
     * Helper to build a Fusion view object
     *
     * @return FusionView
     */
    protected function buildView()
    {
        $view = new FusionView();

        $httpRequest = Request::createFromEnvironment();
        $request = $httpRequest->createActionRequest();

        $uriBuilder = new UriBuilder();
        $uriBuilder->setRequest($request);

        $this->controllerContext = new ControllerContext(
            $request,
            new Response(),
            new Arguments([]),
            $uriBuilder
        );

        $view->setControllerContext($this->controllerContext);
        $view->disableFallbackView();
        $view->setPackageKey('Flowpack.Listable');
        $view->setFusionPathPattern(__DIR__ . '/Fixtures/');

        return $view;
    }
}
