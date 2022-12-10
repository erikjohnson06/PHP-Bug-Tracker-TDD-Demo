<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\Configuration;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class FilterFileCollectionIterator implements \Countable, \Iterator
{
    /**
     * @var FilterFile[]
     */
    private $files;

    /**
     * @var int
     */
    private $position;

    public function __construct(FilterFileCollection $files)
    {
        $this->files = $files->asArray();
    }

    public function count(): int
    {
        return \iterator_count($this);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return $this->position < \count($this->files);
    }

    public function key(): int
    {
        return $this->position;
    }

    public function current(): FilterFile
    {
        return $this->files[$this->position];
    }

    public function next(): void
    {
        $this->position++;
    }
}