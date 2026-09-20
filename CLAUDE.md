# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

PHP ETL Abstraction Layer (`ang3/php-etl`), namespace `Ang3\Component\ETL\`. Requires PHP >= 8.2. This is a library (composer `type: component`), not an application — there is no entrypoint to run, only the source under `src/`.

## Language

- Always communicate with the user in French unless explicitly asked otherwise.
- Explanations, plans, reviews, summaries, questions and status updates must be written in French.
- All source code must remain in English.
- All identifiers, class names, method names, variable names, comments, PHPDoc and technical code documentation must remain in English.
- Keep literal command output, error messages and third-party messages in their original language when quoting them.
- Git commit messages must remain in English if a commit is explicitly requested.

## Commands

Run natively (composer deps are already vendored) or inside the Docker PHP container via `make bash`.

```bash
composer install              # install dependencies

bin/check-code                # static analysis: vendor/bin/phpstan analyse src (level: max, see phpstan.neon)
bin/fix-code                  # code style: vendor/bin/php-cs-fixer fix src, then tests
bin/test                      # vendor/bin/phpunit
```

All three scripts forward extra args (`"$@"`) to the underlying tool, e.g. `bin/check-code --no-progress` or `bin/test --filter=SomeTest`.

Note: there is currently no `tests/` directory or PHPUnit config in the repo, despite `composer.json` declaring the `Ang3\Component\ETL\Tests\` PSR-4 namespace at `tests/` and `bin/test`/`bin/fix-code` referencing it — these will need to be created (with a `phpunit.xml`) before `bin/test` or the `tests/` half of `bin/fix-code` can run.

Docker (optional, mirrors CI environment): `make build`, `make up`, `make bash` (shell into the `php` container), `make down`, `make logs`.

## Architecture

The library processes a dataset row-by-row through a fixed pipeline, converting each input row into an output row while collecting errors along the way. Everything is built around small, replaceable interfaces in `src/Contract/`, with default implementations alongside them in `src/{Context,Factory,Processor,Resolver,Registry,Iterator,Metadata}/`.

### Core flow

`Engine` (`src/Engine.php`) is the only public façade: it wraps a `PipelineInterface` and calls `process(DatasetInterface $dataset): EtlReport`.

`DefaultPipeline` (`src/Pipeline/DefaultPipeline.php`) does the actual work, for each row from `DatasetInterface::getRows()`:

1. Creates a fresh output row via `OutputRowFactoryInterface` (default: `DefaultOutputRowFactory` → `RowMetadata`).
2. Creates a per-row `ContextInterface` via `ContextFactoryInterface`, binding the dataset, the input row, and the output row together (default: `DefaultContextFactory` → `DefaultContext`).
3. For every `FieldMetadata` declared by `DatasetInterface::getFields()`, calls `FieldProcessorInterface::process($field, $context)`.
4. Wraps the input/output pair in a `ProcessedRow` and hands it to `WriterInterface::write()`.
5. Tallies counts on the returned `EtlReport`.

### Field processing

`DefaultFieldProcessor` (`src/Processor/DefaultFieldProcessor.php`) runs two steps per field and wraps unexpected throwables into a `FieldProcessingException` tagged with the stage where they occurred (`ErrorStage::FieldResolution` or `ErrorStage::FieldTransformation`); `EtlException`s are left untouched (not re-wrapped):

1. **Resolve** the raw value via `FieldValueResolverInterface`. The default (`DefaultFieldValueResolver`) reads from `$context->getInput()` either directly by the field's `reference`, or — when the field has `FieldSourceMetadata` — by asking `HeaderResolverInterface` to find a matching input key among `source->key` and `source->aliases` (`DefaultHeaderResolver`). If nothing matches and the source is `required`, it throws `MissingRequiredFieldValueException`; otherwise it falls back to `source->default`.
2. **Transform** the raw value via the `FieldTransformerRegistry`, which holds an ordered list of `FieldTransformerInterface` and returns the first one whose `supports(FieldMetadata)` matches (throws `MissingTransformerException` if none do).

The result is written onto the output row via `$context->set($field->reference, $value)`.

`FieldMetadata` (`src/Metadata/FieldMetadata.php`) is the declarative description of one field: a `reference`, a `type` string (used to pick a transformer), an optional `FieldTargetMetadata` (marks the field as having an output destination — `isVirtual()` is true when there is none), an optional `FieldSourceMetadata` (input key/aliases/default/required), and free-form `options`.

### Context

`ContextInterface` (extended by `CacheAwareContextInterface`) is the per-row state object passed through resolution/transformation/writing. `DefaultContext::get()` is a "smart getter" that resolves a key in this order: per-row cache (`remember`/`recall`) → output/transformed values → raw input values. `input()`/`output()` give direct access to the raw `RowInterface` / `MutableRowInterface`. `rowIndex()`/`sourceLineNumber()` are populated only when the input row implements `PositionedRowInterface` (e.g. `IndexedRowMetadata`).

### Rows

`RowInterface` is a read-only `get/has/all` map; `MutableRowInterface` adds `set`. `RowMetadata` (`src/Metadata/RowMetadata.php`) is the default mutable implementation (also `ArrayAccess`) and is what output rows are made of by default. `IndexedRowMetadata` is an immutable, positioned (`PositionedRowInterface`) input row. `ArrayRowIterator` is a trivial in-memory `RowIteratorInterface`.

### Error handling

Exceptions under `src/Exception/` all extend the abstract `EtlException`, which carries an `EtlErrorCode` (`Contract/Enum/EtlErrorCode.php`), free-form `errorParameters`, and an optional application-specific `appCode`/`appCodeParameters` for consumers to layer their own error codes on top. Two abstract subclasses distinguish error *type*: `ValidationEtlException` (bad/missing data, e.g. `MissingRequiredFieldValueException`, `InvalidFieldValueException`, `UnsupportedFieldValueException`) vs. `TechnicalEtlException` (engine/config problems, e.g. `MissingTransformerException`, `InvalidFieldTransformerException`, `FieldProcessingException`). Exceptions that know which pipeline stage they occurred at implement `StagedExceptionInterface`.

`ErrorFactoryInterface` (default: `DefaultErrorFactory`) turns any `\Throwable` into an immutable `EtlError` (`src/Error/EtlError.php`) for reporting: it walks the `getPrevious()` chain to find the innermost `EtlException` (for the error/app codes and parameters), the type (`ValidationEtlException`/`TechnicalEtlException` → `ErrorType`), and the stage (`StagedExceptionInterface` → `ErrorStage`), falling back to `ErrorType::Technical` and the stage passed by the caller when nothing in the chain matches.

`EtlReport` (`src/Result/EtlReport.php`) is the mutable aggregate returned by `PipelineInterface::process()`: row counters (processed/valid/invalid/written), a capped sample of `EtlError`s (`maxSampleErrors`, default 100 — full persistence is expected to be handled by a writer/error sink, not the report), and an optional single `fatalError`. Note `DefaultPipeline` currently only increments counters and never calls `addError`/`markFatal`/`incrementInvalidRows` itself — error reporting wiring into the pipeline loop is not yet implemented there.

### Extension points (implement these to integrate the library)

- `DatasetInterface` — supplies the field schema (`getFields()`) and the rows to iterate (`getRows()`).
- `WriterInterface` — persists each `ProcessedRow` (implementations should skip virtual fields, i.e. fields with no `FieldTargetMetadata`).
- `FieldTransformerInterface` — add to `FieldTransformerRegistry` to support a given `FieldMetadata->type`.
- `FieldValueResolverInterface` / `HeaderResolverInterface` — customize how raw values are located in the input row.
- `ContextFactoryInterface` / `OutputRowFactoryInterface` — customize the context/output row implementations used per row.

## Code style

- Prefer `readonly` classes/properties and constructor promotion, matching existing code.
- `.php-cs-fixer.dist.php` applies `@Symfony` rules plus a required license header on every file; run `bin/fix-code` rather than hand-formatting.
- `phpstan.neon` runs at `level: max` against `src/` only, with `treatPhpDocTypesAsCertain: false`.
