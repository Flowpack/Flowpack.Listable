<?php
namespace Flowpack\Listable\Tests\Functional\TypoScriptObjects;

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Flowpack\Listable\Tests\Functional\TypoScriptObjects\AbstractTypoScriptObjectTest;

/**
 * Testcase for the Listable object
 */
class ListableTest extends AbstractTypoScriptObjectTest
{
    /**
     * @test
     */
    public function basicListingWorks()
    {
        $newNode1 = $this->getMockBuilder(NodeInterface::class)->getMock();
        $newNode1->method('getProperties')->willReturn(array('title' => 'Hello world'));
        $newNode2 = $this->getMockBuilder(NodeInterface::class)->getMock();
        $newNode2->method('getProperties')->willReturn(array('title' => 'Another hello world!'));

        $view = $this->buildView();
        $view->setFusionPath('listable/basicListing');
        $view->assign('collection', array($newNode1, $newNode2));
        $this->assertXmlStringEqualsXmlString(
          '<ul>
            <li>Hello world</li>
            <li>Another hello world!</li>
          </ul>',
          $view->render()
        );
    }
}
