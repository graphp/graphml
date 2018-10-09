<?php

namespace Graphp\GraphML;

use Fhaculty\Graph\Exporter\ExporterInterface;
use Fhaculty\Graph\Graph;
use SimpleXMLElement;
use Fhaculty\Graph\Edge\Directed;

class Exporter implements ExporterInterface
{
    /** @internal */
    const SKEL = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<graphml xmlns="http://graphml.graphdrawing.org/xmlns"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://graphml.graphdrawing.org/xmlns
     http://graphml.graphdrawing.org/xmlns/1.0/graphml.xsd">
</graphml>
EOL;

    /**
     * Exports the given graph instance.
     *
     * ```php
     * $graph = new Fhaculty\Graph\Graph();
     *
     * $a = $graph->createVertex('a');
     * $b = $graph->createVertex('b');
     * $a->createEdgeTo($b);
     *
     * $exporter = new Graphp\GraphML\Exporter();
     * $data = $exporter->getOutput($graph);
     *
     * file_put_contents('example.graphml', $data);
     * ```
     *
     * This method only supports exporting the basic graph structure, with all
     * vertices and directed and undirected edges.
     *
     * Note that none of the attributes attached to any objects nor any of the
     * "advanced concepts" of GraphML (Nested Graphs, Hyperedges and Ports) are
     * currently implemented. We welcome PRs!
     *
     * @param Graph $graph
     * @return string
     */
    public function getOutput(Graph $graph)
    {
        $root = new SimpleXMLElement(self::SKEL);

        $graphElem = $root->addChild('graph');
        $graphElem['edgeDefault'] = 'undirected';

        foreach ($graph->getVertices()->getMap() as $id => $vertex) {
            /* @var $vertex \Fhaculty\Graph\Vertex */
            $vertexElem = $graphElem->addChild('node');
            $vertexElem['id'] = $id;
        }

        foreach ($graph->getEdges() as $edge) {
            /* @var $edge \Fhaculty\Graph\Edge\Base */
            $edgeElem = $graphElem->addChild('edge');
            $edgeElem['source'] = $edge->getVertices()->getVertexFirst()->getId();
            $edgeElem['target'] = $edge->getVertices()->getVertexLast()->getId();

            if ($edge instanceof Directed) {
                $edgeElem['directed'] = 'true';
            }
        }

        return $root->asXML();
    }
}
