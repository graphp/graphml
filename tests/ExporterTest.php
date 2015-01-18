<?php

use Fhaculty\Graph\Graph;
use Graphp\GraphML\Exporter;

class ExporterTest extends TestCase
{
    private $exporter;

    public function setUp()
    {
        $this->exporter = new Exporter();
    }

    public function testEmpty()
    {
        $graph = new Graph();

        $output = $this->exporter->getOutput($graph);
        $xml = new SimpleXMLElement($output);

        $this->assertEquals(1, count($xml));
        $this->assertEquals(1, count($xml->graph));
        $this->assertEquals(0, count($xml->graph->children()));
    }

    public function testSimple()
    {
        // 1 -- 2
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $v2 = $graph->createVertex(2);
        $v1->createEdge($v2);

        $output = $this->exporter->getOutput($graph);
        $xml = new SimpleXMLElement($output);

        $this->assertEquals(1, count($xml->graph->edge));

        $edgeElem = $xml->graph->edge;
        $this->assertEquals('1', (string)$edgeElem['source']);
        $this->assertEquals('2', (string)$edgeElem['target']);
        $this->assertFalse(isset($edgeElem['directed']));
    }

    public function testSimpleDirected()
    {
        // 1 -> 2
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $v2 = $graph->createVertex(2);
        $v1->createEdgeTo($v2);

        $output = $this->exporter->getOutput($graph);
        $xml = new SimpleXMLElement($output);

        $this->assertEquals(1, count($xml->graph->edge));

        $edgeElem = $xml->graph->edge;
        $this->assertEquals('1', (string)$edgeElem['source']);
        $this->assertEquals('2', (string)$edgeElem['target']);
        $this->assertEquals('true', (string)$edgeElem['directed']);
    }
}
