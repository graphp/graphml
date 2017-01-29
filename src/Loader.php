<?php

namespace Graphp\GraphML;

use SimpleXMLElement;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Attribute\AttributeAware;

class Loader
{
    /**
     * @param string $contents
     * @return Graph
     */
    public function loadContents($contents)
    {
        return $this->loadXml(new SimpleXMLElement($contents));
    }

    /**
     * @param SimpleXMLElement $root
     * @return Graph
     */
    private function loadXml(SimpleXMLElement $root)
    {
        $graph = new Graph();

        // parse all attribute keys
        $keys = array();
        foreach ($root->key as $keyElem) {
            $keys[(string)$keyElem['id']] = array(
                'name' => (string)$keyElem['attr.name'],
                'type' => (string)$keyElem['attr.type'],
                'for'  => (isset($keyElem['for']) ? (string)$keyElem['for'] : 'all'),
                'default' => (isset($keyElem->default) ? $this->castAttribute((string)$keyElem->default, (string)$keyElem['attr.type']) : null)
            );
        }

        // load global graph settings
        $edgedefault = ((string)$root->graph['edgedefault'] === 'directed');
        $this->loadAttributes($root->graph, $graph, $keys);

        // load all vertices (known as "nodes" in GraphML)
        foreach ($root->graph->node as $nodeElem) {
            $vertex = $graph->createVertex((string)$nodeElem['id']);

            $this->loadAttributes($nodeElem, $vertex, $keys);
        }

        // load all edges
        foreach ($root->graph->edge as $edgeElem) {
            $source = $graph->getVertex((string)$edgeElem['source']);
            $target = $graph->getVertex((string)$edgeElem['target']);

            $directed = $edgedefault;
            if (isset($edgeElem['directed'])) {
                $directed = ((string)$edgeElem['directed'] === 'true');
            }

            if ($directed) {
                $edge = $source->createEdgeTo($target);
            } else {
                $edge = $source->createEdge($target);
            }

            $this->loadAttributes($edgeElem, $edge, $keys);
        }

        return $graph;
    }

    /**
     * @param SimpleXMLElement $xml
     * @param AttributeAware $target
     * @param array $keys
     */
    private function loadAttributes(SimpleXMLElement $xml, AttributeAware $target, array $keys)
    {
        // apply all default values for this type
        $type = $xml->getName();
        foreach ($keys as $key) {
            if (isset($key['default']) && ($key['for'] === $type || $key['for'] === 'all')) {
                $target->setAttribute($key['name'], $key['default']);
            }
        }

        // apply all data attributes for this element
        foreach ($xml->data as $dataElem) {
            $key = $keys[(string)$dataElem['key']];
            $target->setAttribute($key['name'], $this->castAttribute((string)$dataElem, $key['type']));
        }
    }

    /**
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    private function castAttribute($value, $type)
    {
        if ($type === 'boolean') {
            return ($value === 'true');
        } elseif ($type === 'int' || $type === 'long') {
            return (int)$value;
        } elseif ($type === 'float' || $type === 'double') {
            return (float)$value;
        }
        return $value;
    }
}
