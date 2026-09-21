PHP ETL Engine
==============

[![CI](https://github.com/Ang3/php-etl-engine/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/Ang3/php-etl-engine/actions/workflows/ci.yml)
[![PHP Version Require](https://img.shields.io/badge/php-%3E%3D8.2-8892BF.svg)](composer.json)

The PHP ETL Engine is a small, contract-first abstraction layer for building Extract-Transform-Load
pipelines. It turns the rows of a dataset into output rows through a configurable, per-field resolution
and transformation process, collecting validation and technical errors along the way instead of failing
the whole run on the first mistake.

Every stage of the pipeline — how a field's raw value is resolved, how it is transformed, how a
processed row is written, how an unexpected exception becomes a reportable error — is defined as a
small interface under `Ang3\Component\ETL\Contract\`, each with a default implementation you can use
as-is or replace with your own.

Installation
------------

```bash
composer require ang3/php-etl
```

Requirements
------------

  * PHP >= 8.2

Usage
-----

```php
use Ang3\Component\ETL\Engine;
use Ang3\Component\ETL\Factory\DefaultContextFactory;
use Ang3\Component\ETL\Factory\DefaultErrorFactory;
use Ang3\Component\ETL\Factory\DefaultOutputRowFactory;
use Ang3\Component\ETL\Pipeline\DefaultPipeline;
use Ang3\Component\ETL\Processor\DefaultFieldProcessor;
use Ang3\Component\ETL\Registry\FieldTransformerRegistry;
use Ang3\Component\ETL\Resolver\DefaultFieldValueResolver;
use Ang3\Component\ETL\Resolver\DefaultHeaderResolver;

$transformers = new FieldTransformerRegistry();
$transformers->add($myFieldTransformer); // your own FieldTransformerInterface implementation(s)

$pipeline = new DefaultPipeline(
    contextFactory: new DefaultContextFactory(),
    fieldProcessor: new DefaultFieldProcessor(
        new DefaultFieldValueResolver(new DefaultHeaderResolver()),
        $transformers,
    ),
    outputRowFactory: new DefaultOutputRowFactory(),
    writer: $myWriter, // your own WriterInterface implementation
    errorFactory: new DefaultErrorFactory(),
);

$engine = new Engine($pipeline);
$report = $engine->process($myDataset); // your own DatasetInterface implementation

if ($report->hasErrors()) {
    foreach ($report->sampleErrors() as $error) {
        // $error->message, $error->stage, $error->fieldReference(), ...
    }
}
```

`$report` is an `Ang3\Component\ETL\Result\EtlReport`: processed/valid/invalid/written row counters,
plus a capped sample of the `Ang3\Component\ETL\Error\EtlError`s collected while processing.

By default, a field error never aborts the run: every field of a row is attempted, each failure is
collected, and the row is still handed to the writer (marked invalid) so it can decide what to do with
it. A writer failure, on the other hand, can be made fatal on the spot by passing `strict: true` to
`DefaultPipeline` — see `Ang3\Component\ETL\Pipeline\DefaultPipeline::OPTION_STRICT_WRITE_ERRORS`.

Extension points
----------------

Implement these interfaces to integrate the engine with your own data sources and destinations:

  * `DatasetInterface` — supplies the field schema and the rows to iterate.
  * `WriterInterface` — persists each processed row.
  * `FieldTransformerInterface` — transforms a resolved raw value; register it on a
    `FieldTransformerRegistry`, or compose several with `ChainFieldTransformer`.
  * `FieldValueResolverInterface` / `HeaderResolverInterface` — customize how raw values are located in
    an input row.
  * `ContextFactoryInterface` / `OutputRowFactoryInterface` — customize the per-row context and output
    row implementations.

Resources
---------

  * [Report issues](https://github.com/Ang3/php-etl-engine/issues) and
    [send Pull Requests](https://github.com/Ang3/php-etl-engine/pulls)
    in the [main repository](https://github.com/Ang3/php-etl-engine)
