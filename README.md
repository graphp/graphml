# graphp/graphml

[![CI status](https://github.com/graphp/graphml/workflows/CI/badge.svg)](https://github.com/graphp/graphml/actions)

[GraphML](http://graphml.graphdrawing.org/) is an XML-based file format for graphs

> Note: This project is in early beta stage! Feel free to report any issues you encounter.

**Table of contents**

* [Usage](#usage)
  * [Exporter](#exporter)
    * [getOutput()](#getoutput)
  * [Loader](#loader)
    * [loadContents()](#loadcontents)
* [Install](#install)
* [Tests](#tests)
* [License](#license)

## Usage

### Exporter

#### getOutput()

The `getOutput(Graph $graph): string` method can be used to
export the given graph instance.

```php
$graph = new Fhaculty\Graph\Graph();

$a = $graph->createVertex('a');
$b = $graph->createVertex('b');
$a->createEdgeTo($b);

$exporter = new Graphp\GraphML\Exporter();
$data = $exporter->getOutput($graph);

file_put_contents('example.graphml', $data);
```

This method only supports exporting the basic graph structure, with all
vertices and directed and undirected edges.

Note that none of the attributes attached to any objects nor any of the
"advanced concepts" of GraphML (Nested Graphs, Hyperedges and Ports) are
currently implemented. We welcome PRs!

### Loader

#### loadContents()

The `loadContents(string $contents): Graph` method can be used to
load a graph instance from the given GraphML contents.

```php
$data = file_get_contents('example.graphml');

$loader = new Graphp\GraphML\Loader();
$graph = $loader->loadContents($data);

foreach ($graph->getVertices() as $vertex) {
    foreach ($vertex->getVerticesEdgeTo() as other) {
        echo $vertex->getId() . ' connected with ' . $other->getId() . PHP_EOL;
    }
}
```

This method supports loading the graph, all vertices and directed and
undirected edges among with any attributes attached from the GraphML
source.

Note that neither of the "advanced concepts" of GraphML (Nested Graphs,
Hyperedges and Ports) are currently implemented. We welcome PRs!

## Install

The recommended way to install this library is [through composer](http://getcomposer.org). [New to composer?](http://getcomposer.org/doc/00-intro.md)

```JSON
{
    "require": {
        "graphp/graphml": "~0.1.0"
    }
}
```

This project aims to run on any platform and thus does not require any PHP
extensions and supports running on legacy PHP 5.3 through current PHP 7+ and
HHVM.
It's *highly recommended to use PHP 7+* for this project.

## Tests

To run the test suite, you first need to clone this repo and then install all
dependencies [through Composer](https://getcomposer.org):

```bash
$ composer install
```

To run the test suite, go to the project root and run:

```bash
$ php vendor/bin/phpunit
```

## License

Released under the terms of the permissive [MIT license](http://opensource.org/licenses/MIT).
