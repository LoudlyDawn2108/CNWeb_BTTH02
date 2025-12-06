<?php

namespace Functional;

use ArrayIterator;
use Closure;
use Traversable;

class Collection implements \Countable, \IteratorAggregate, \JsonSerializable {
    protected array $items;

    public function __construct(array $items = []) {
        $this->items = $items;
    }

    /**
     * Create a new collection instance.
     */
    public static function make(array $items = []): self {
        return new self($items);
    }

    /**
     * Apply a callback to each item in the collection.
     */
    public function map(callable $callback): self {
        return new self(array_map($callback, $this->items));
    }

    /**
     * Map each item and flatten the result.
     */
    public function flatMap(callable $callback): self {
        $mapped = array_map($callback, $this->items);
        $flattened = [];
        foreach ($mapped as $item) {
            if (is_array($item) || $item instanceof self) {
                foreach ($item as $subItem) {
                    $flattened[] = $subItem;
                }
            } else {
                $flattened[] = $item;
            }
        }
        return new self($flattened);
    }

    /**
     * Filter the collection using the given callback.
     */
    public function filter(?callable $callback = null): self {
        if ($callback) {
            return new self(array_values(array_filter($this->items, $callback)));
        }
        return new self(array_values(array_filter($this->items)));
    }

    /**
     * Reduce the collection to a single value.
     */
    public function reduce(callable $callback, $initial = null) {
        return array_reduce($this->items, $callback, $initial);
    }

    /**
     * Execute a callback over each item.
     */
    public function each(callable $callback): self {
        foreach ($this->items as $key => $item) {
            if ($callback($item, $key) === false) {
                break;
            }
        }
        return $this;
    }

    /**
     * Push an item onto the end of the collection.
     */
    public function push($value): self {
        $items = $this->items;
        $items[] = $value;
        return new self($items);
    }

    /**
     * Merge the collection with the given items.
     */
    public function merge(array|self $items): self {
        if ($items instanceof self) {
            $items = $items->toArray();
        }
        return new self(array_merge($this->items, $items));
    }

    /**
     * Get the first item from the collection.
     */
    public function first(?callable $callback = null, $default = null) {
        if (is_null($callback)) {
            if (empty($this->items)) {
                return $default;
            }
            return reset($this->items);
        }

        foreach ($this->items as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * Get the last item from the collection.
     */
    public function last(?callable $callback = null, $default = null) {
        if (is_null($callback)) {
            if (empty($this->items)) {
                return $default;
            }
            return end($this->items);
        }
        
        $reversed = array_reverse($this->items, true);
        foreach ($reversed as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }
        
        return $default;
    }

    /**
     * Check if the collection contains a given value.
     */
    public function contains($value): bool {
        if ($value instanceof Closure) {
            return !is_null($this->first($value));
        }
        return in_array($value, $this->items, true);
    }

    /**
     * Sort the collection.
     */
    public function sort(?callable $callback = null): self {
        $items = $this->items;
        if ($callback) {
            usort($items, $callback);
        } else {
            sort($items);
        }
        return new self($items);
    }

    /**
     * Reverse the collection.
     */
    public function reverse(): self {
        return new self(array_reverse($this->items));
    }

    /**
     * Get the underlying array.
     */
    public function toArray(): array {
        return $this->items;
    }

    public function count(): int {
        return count($this->items);
    }

    public function isEmpty(): bool {
        return empty($this->items);
    }

    public function getIterator(): Traversable {
        return new ArrayIterator($this->items);
    }

    public function jsonSerialize(): mixed {
        return $this->items;
    }
}
