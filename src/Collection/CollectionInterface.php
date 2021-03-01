<?php declare(strict_types=1);

namespace Hi\Helpers\Collection;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Serializable;

interface CollectionInterface extends 
    ArrayAccess,
    Countable,
    IteratorAggregate,
    JsonSerializable,
    Serializable
{
    public function __get(string $element);

    public function __isset(string $element): bool;

    public function __set(string $element, $value);

    public function __unset(string $element);

    public function clear();

    public function get(string $element, $defaultValue = null, string $cast = null);

    public function getKeys(bool $insensitive = true): array;

    public function getValues(): array;

    public function has(string $element): bool;

    public function init(array $data = []);

    public function remove(string $element);

    public function set(string $element, $value);

    public function toArray(): array;

    public function toJson(int $options = 79): string;
}
