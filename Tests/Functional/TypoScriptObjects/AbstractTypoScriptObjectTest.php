<?php
namespace Flowpack\Listable\Tests\Functional\TypoScriptObjects;

use TYPO3\Flow\Http\Request;
use TYPO3\Flow\Http\Response;
use TYPO3\Flow\Mvc\Controller\Arguments;
use TYPO3\Flow\Mvc\Controller\ControllerContext;
use TYPO3\Flow\Mvc\Routing\UriBuilder;

/**
 * Testcase for the TypoScript View
 *
 */
abstract class AbstractTypoScriptObjectTest extends \TYPO3\Flow\Tests\FunctionalTestCase
{
    /**
     * @var ControllerContext
     */
    protected $controllerContext;

    /**
     * Helper to build a TypoScript view object
     *
     * @return \TYPO3\TypoScript\View\TypoScriptView
     */
    protected function buildView()
    {
        $view = new \TYPO3\TypoScript\View\TypoScriptView();

        $httpRequest = Request::createFromEnvironment();
        $request = $httpRequest->createActionRequest();

        $uriBuilder = new UriBuilder();
        $uriBuilder->setRequest($request);

        $this->controllerContext = new ControllerContext(
            $request,
            new Response(),
            new Arguments(array()),
            $uriBuilder
        );

        $view->setControllerContext($this->controllerContext);
        $view->disableFallbackView();
        $view->setPackageKey('Flowpack.Listable');
        $view->setTypoScriptPathPattern(__DIR__ . '/Fixtures/');

        return $view;
    }
}
