<?php

namespace Graphp\Tests\GraphML;

use Graphp\GraphML\Loader;

class LoaderTest extends TestCase
{
    private $loader;

    /**
     * @before
     */
    public function setUpLoader()
    {
        $this->loader = new Loader();
    }

    public function testEmpty()
    {
        $data = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<graphml xmlns="http://graphml.graphdrawing.org/xmlns"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://graphml.graphdrawing.org/xmlns
     http://graphml.graphdrawing.org/xmlns/1.0/graphml.xsd">
  <graph id="G" edgedefault="undirected">
  </graph>
</graphml>
EOL;

        $graph = $this->loader->loadContents($data);

        $this->assertCount(0, $graph->getVertices());
    }

    public function testSimpleGraph()
    {
        $data = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<graphml xmlns="http://graphml.graphdrawing.org/xmlns"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://graphml.graphdrawing.org/xmlns
     http://graphml.graphdrawing.org/xmlns/1.0/graphml.xsd">
  <graph id="G" edgedefault="undirected">
    <node id="n0"/>
    <node id="n1"/>
    <node id="n2"/>
    <node id="n3"/>
    <node id="n4"/>
    <node id="n5"/>
    <node id="n6"/>
    <node id="n7"/>
    <node id="n8"/>
    <node id="n9"/>
    <node id="n10"/>
    <edge source="n0" target="n2"/>
    <edge source="n1" target="n2"/>
    <edge source="n2" target="n3"/>
    <edge source="n3" target="n5"/>
    <edge source="n3" target="n4"/>
    <edge source="n4" target="n6"/>
    <edge source="n6" target="n5"/>
    <edge source="n5" target="n7"/>
    <edge source="n6" target="n8"/>
    <edge source="n8" target="n7"/>
    <edge source="n8" target="n9"/>
    <edge source="n8" target="n10"/>
  </graph>
</graphml>
EOL;

        $graph = $this->loader->loadContents($data);

        $this->assertCount(11, $graph->getVertices());
        $this->assertCount(12, $graph->getEdges());
    }

    public function testEdgeUndirected()
    {
        $data = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<graphml xmlns="http://graphml.graphdrawing.org/xmlns"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://graphml.graphdrawing.org/xmlns
     http://graphml.graphdrawing.org/xmlns/1.0/graphml.xsd">
  <graph id="G" edgedefault="undirected">
    <node id="n0"/>
    <edge source="n0" target="n0" directed="false"/>
    <edge source="n0" target="n0"/>
  </graph>
</graphml>
EOL;

        $graph = $this->loader->loadContents($data);

        $this->assertCount(2, $graph->getEdges());
        $this->assertInstanceOf('Fhaculty\Graph\Edge\Undirected', $graph->getEdges()->getEdgeFirst());
        $this->assertInstanceOf('Fhaculty\Graph\Edge\Undirected', $graph->getEdges()->getEdgeLast());
    }

    public function testEdgeDirected()
    {
        $data = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<graphml xmlns="http://graphml.graphdrawing.org/xmlns"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://graphml.graphdrawing.org/xmlns
     http://graphml.graphdrawing.org/xmlns/1.0/graphml.xsd">
  <graph id="G" edgedefault="directed">
    <node id="n0"/>
    <edge source="n0" target="n0" directed="true"/>
    <edge source="n0" target="n0"/>
  </graph>
</graphml>
EOL;

        $graph = $this->loader->loadContents($data);

        $this->assertCount(2, $graph->getEdges());
        $this->assertInstanceOf('Fhaculty\Graph\Edge\Directed', $graph->getEdges()->getEdgeFirst());
        $this->assertInstanceOf('Fhaculty\Graph\Edge\Directed', $graph->getEdges()->getEdgeLast());
    }

    public function testAttributeTypes()
    {
        $data = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<graphml xmlns="http://graphml.graphdrawing.org/xmlns"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://graphml.graphdrawing.org/xmlns
     http://graphml.graphdrawing.org/xmlns/1.0/graphml.xsd">
  <key id="d0" for="node" attr.name="string" attr.type="string"></key>
  <key id="d1" for="node" attr.name="boolean" attr.type="boolean"></key>
  <key id="d2" for="node" attr.name="float" attr.type="float"></key>
  <key id="d3" for="node" attr.name="int" attr.type="int"></key>
  <graph id="G" edgedefault="undirected">
    <node id="n0">
      <data key="d0">text</data>
      <data key="d1">true</data>
      <data key="d2">4.5</data>
      <data key="d3">3</data>
    </node>
  </graph>
</graphml>
EOL;

        $graph = $this->loader->loadContents($data);

        $vertex = $graph->getVertices()->getVertexFirst();

        $this->assertEquals('n0', $vertex->getId());
        $this->assertSame('text', $vertex->getAttribute('string'));
        $this->assertSame(true, $vertex->getAttribute('boolean'));
        $this->assertSame(4.5, $vertex->getAttribute('float'));
        $this->assertSame(3, $vertex->getAttribute('int'));
    }

    public function testAttributeDefault()
    {
        $data = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<graphml xmlns="http://graphml.graphdrawing.org/xmlns"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://graphml.graphdrawing.org/xmlns
     http://graphml.graphdrawing.org/xmlns/1.0/graphml.xsd">
  <key id="d0" for="node" attr.name="color" attr.type="string">
    <default>yellow</default>
  </key>
  <graph id="G" edgedefault="undirected">
    <node id="n0"/>
  </graph>
</graphml>
EOL;

        $graph = $this->loader->loadContents($data);

        $vertex = $graph->getVertices()->getVertexFirst();

        $this->assertEquals('n0', $vertex->getId());
        $this->assertEquals('yellow', $vertex->getAttribute('color'));
    }
}
