<?php


namespace App\Collections;

use Iterator;

abstract class Collection implements Iterator
{
    protected int $pointer = 0;
    protected int $total = 0;
    protected array $objects = [];
    protected array $raw = [];

    public function __construct(array $raw = [])
    {
        if (!empty($raw)) {
            $this->raw = $raw;
            $this->total = count($raw);
        }
    }

    private function getRow(int $num): ?Collection
    {
        if ($num >= $this->total || $num < 0) {
            return null;
        }

        if (isset($this->objects[$num])) {
            return $this->objects[$num];
        }

        if (isset($this->raw[$num])) {
            $this->objects[$num] = $this->createObject($this->raw[$num]);
            return $this->objects[$num];
        }

        return null;
    }

    public function current(): ?Collection
    {
        return $this->getRow($this->pointer);
    }

    public function next(): ?Collection
    {
        $this->pointer++;
        return $this->current();
    }

    public function key(): int
    {
        return $this->pointer;
    }

    public function valid(): bool
    {
        return (!is_null($this->current()));
    }

    public function rewind()
    {
        $this->pointer = 0;
    }

    abstract protected function createObject(array $raw);
}
