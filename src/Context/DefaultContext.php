<?php

/*
 * This file is part of package ang3/php-etl-engine
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\ETL\Context;

use Ang3\Component\ETL\Contract\CacheAwareContextInterface;
use Ang3\Component\ETL\Contract\DatasetInterface;
use Ang3\Component\ETL\Contract\MutableRowInterface;
use Ang3\Component\ETL\Contract\RowInterface;

class DefaultContext implements CacheAwareContextInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $options = [];

    /**
     * @var array<string, mixed>
     */
    private array $cache = [];

    public function __construct(
        private readonly RowInterface $input,
        private readonly MutableRowInterface $output,
        private readonly DatasetInterface $dataset,
    ) {
    }

    public function input(): RowInterface
    {
        return $this->input;
    }

    public function output(): MutableRowInterface
    {
        return $this->output;
    }

    public function dataset(): DatasetInterface
    {
        return $this->dataset;
    }

    public function getInput(string $key, mixed $default = null): mixed
    {
        return $this->input->has($key) ? $this->input->get($key) : $default;
    }

    public function hasInput(string $key): bool
    {
        return $this->input->has($key);
    }

    public function getOutput(string $key, mixed $default = null): mixed
    {
        return $this->output->has($key) ? $this->output->get($key) : $default;
    }

    public function hasOutput(string $key): bool
    {
        return $this->output->has($key);
    }

    /**
     * Smart getter.
     *
     * Resolution order:
     * 1. cache
     * 2. output / transformed values
     * 3. input / raw values
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if ($this->hasCached($key)) {
            return $this->recall($key);
        }

        if ($this->hasOutput($key)) {
            return $this->getOutput($key);
        }

        if ($this->hasInput($key)) {
            return $this->getInput($key);
        }

        return $default;
    }

    public function has(string $key): bool
    {
        return $this->hasCached($key)
            || $this->hasOutput($key)
            || $this->hasInput($key);
    }

    public function set(string $key, mixed $value): void
    {
        $this->output->set($key, $value);
    }

    public function remember(string $key, mixed $value): void
    {
        $this->cache[$key] = $value;
    }

    public function recall(string $key, mixed $default = null): mixed
    {
        return array_key_exists($key, $this->cache)
            ? $this->cache[$key]
            : $default;
    }

    public function hasCached(string $key): bool
    {
        return array_key_exists($key, $this->cache);
    }

    public function setOption(string $key, mixed $value): void
    {
        $this->options[$key] = $value;
    }

    public function getOption(string $key, mixed $default = null): mixed
    {
        return array_key_exists($key, $this->options)
            ? $this->options[$key]
            : $default;
    }

    public function hasOption(string $key): bool
    {
        return array_key_exists($key, $this->options);
    }

    public function getRequired(string $key): mixed
    {
        if (!$this->has($key)) {
            throw new \RuntimeException(sprintf('Missing required key "%s"', $key));
        }

        return $this->get($key);
    }
}
