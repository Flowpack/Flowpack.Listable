<?php
namespace Flowpack\Listable\Tests\Functional\FusionObjects;

use Neos\ContentRepository\Domain\Model\NodeInterface;

class ListableTest extends AbstractTypoScriptObjectTest
{
    /**
     * @test
     */
    public function basicListingWorks()
    {
        $newNode1 = $this->getMockBuilder(NodeInterface::class)->getMock();
        $newNode1->method('getProperties')->willReturn(['title' => 'Hello world']);
        $newNode2 = $this->getMockBuilder(NodeInterface::class)->getMock();
        $newNode2->method('getProperties')->willReturn(['title' => 'Another hello world!']);

        $view = $this->buildView();
        $view->setFusionPath('listable/basicListing');
        $view->assign('collection', [$newNode1, $newNode2]);
        $this->assertXmlStringEqualsXmlString(
          '<ul>
            <li>Hello world</li>
            <li>Another hello world!</li>
          </ul>',
          $view->render()
        );
    }
}
