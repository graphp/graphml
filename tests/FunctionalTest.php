<?php

use Fhaculty\Graph\Graph;
use Graphp\GraphML\Exporter;
use Graphp\GraphML\Loader;

class FunctionalTest extends TestCase
{
    public function testMixed()
    {
        // 1 -- 2 -> 3
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $v2 = $graph->createVertex(2);
        $v3 = $graph->createVertex(3);
        $v1->createEdge($v2);
        $v2->createEdgeTo($v3);

        $exporter = new Exporter();
        $output = $exporter->getOutput($graph);

        $loader = new Loader();
        $new = $loader->loadContents($output);

        $this->assertGraphEquals($graph, $new);
    }
}
