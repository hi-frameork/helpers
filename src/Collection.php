<?php declare(strict_types=1);

namespace Hi\Helpers;

use ArrayIterator;
use Hi\Helpers\Collection\CollectionInterface;
use Traversable;

use function strtolower;
use function count;
use function settype;
use function array_keys;
use function array_values;
use function is_object;
use function method_exists;
use function serialize;
use function unserialize;

/**
 * Collection 是一个面向对象的超级数组，它实现了：
 * - [ArrayAccess](https://www.php.net/manual/en/class.arrayaccess.php)
 * - [Countable](https://www.php.net/manual/en/class.countable.php)
 * - [IteratorAggregate](https://www.php.net/manual/en/class.iteratoraggregate.php)
 * - [JsonSerializable](https://www.php.net/manual/en/class.jsonserializable.php)
 * - [Serializable](https://www.php.net/manual/en/class.serializable.php)
 *
 * 它可以用于需要收集数据的应用程序的任何部分，例如访问globals`$\u GET`、`$\u POST`等。
 */
class Collection implements CollectionInterface
{
    /**
     * @$array
     */
    protected $data = [];

    /**
     * @$bool
     */
    protected $insensitive = true;

    /**
     * @$array
     */
    protected $lowerKeys = [];

    /**
     * Collection constructor.
     */
    public function __construct(array $data = [], bool $insensitive = true)
    {
        $this->insensitive = $insensitive;
        $this->init($data);
    }

    /**
     * 数组数据初始化
     */
    public function init(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->setData($key, $value);
        }
    }

    /**
     * magic getter
     */
    public function __get(string $element)
    {
        return $this->get($element);
    }

    /**
     * magic isset
     */
    public function __isset(string $element): bool
    {
        return $this->has($element);
    }

    /**
     * Magic setter
     */
    public function __set(string $element, $value)
    {
        $this->set($element, $value);
    }

    /**
     * Magic unset
     */
    public function __unset(string $element)
    {
        $this->remove($element);
    }

    /**
     * 清空数据
     */
    public function clear()
    {
        $this->data      = [];
        $this->lowerKeys = [];
    }

    /**
     * 计算数组元素个数
     *
     * @see [count](https://php.net/manual/en/countable.count.php)
     */
    public function count():int
    {
        return count($this->data);
    }

    /**
     * 获取指定数据
     *
     * @return mixed
     */
    public function get(
        string $element,
        $defaultValue = null,
        string $cast = null
    ) {
        $element = $this->lowerElement($element);

        $key = $this->lowerKeys[$element] ?? '';
        if (! $key) {
            return $defaultValue;
        }

        $value = $this->data[$key];

        if ($cast) {
            settype($value, $cast);
        }

        return $value;
    }

    /**
     * 将数组包装成迭代器返回
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->data);
    }

    public function getKeys(bool $insensitive = true): array
    {
        if ($insensitive) {
            return array_keys($this->lowerKeys);
        } else {
            return array_keys($this->data);
        }
    }

    public function getValues(): array
    {
        return array_values($this->data);
    }

    /**
     * 确定集合中是否存在特定元素
     */
    public function has(string $element): bool
    {
        $element = $this->lowerElement($element);
        return isset($this->lowerKeys[$element]);
    }

     /**
     * 返回应序列化为 JSON 的数据
     * @see [jsonSerialize](https://php.net/manual/en/jsonserializable.jsonserialize.php)
     */
    public function jsonSerialize(): array
    {
        $records = [];

        foreach ($this->data as $key => $value) {
            if (is_object($value) && method_exists($value, "jsonSerialize")) {
                $records[$key] = $value->{"jsonSerialize"}();
            } else {
                $records[$key] = $value;
            }
        }

        return $records;
    }

    /**
     * Whether a offset exists
     * See [offsetExists](https://php.net/manual/en/arrayaccess.offsetexists.php)
     */
    public function offsetExists($element): bool
    {
        $element = (string) $element;

        return $this->has($element);
    }

    /**
     * Offset to retrieve
     * See [offsetGet](https://php.net/manual/en/arrayaccess.offsetget.php)
     */
    public function offsetGet($element)
    {
        $element = (string) $element;

        return $this->get($element);
    }

    /**
     * Offset to set
     * See [offsetSet](https://php.net/manual/en/arrayaccess.offsetset.php)
     */
    public function offsetSet($element, $value): void
    {
        $element = (string) $element;

        $this->set($element, $value);
    }

    /**
     * Offset to unset
     * See [offsetUnset](https://php.net/manual/en/arrayaccess.offsetunset.php)
     */
    public function offsetUnset($element): void
    {
        $element = (string) $element;

        $this->remove($element);
    }

    /**
     * Delete the element from the collection
     */
    public function remove(string $element)
    {
        if ($this->has($element)) {
            $element = $this->lowerElement($element);

            $data      = $this->data;
            $lowerKeys = $this->lowerKeys;
            $key       = $lowerKeys[$element];

            unset($lowerKeys[$element]);
            unset($data[$key]);

            $this->data = $data;
            $this->lowerKeys = $lowerKeys;
        }
    }

    /**
     * Set an element in the collection
     */
    public function set(string $element, $value)
    {
        $this->setData($element, $value);
    }

    /**
     * String representation of object
     * See [serialize](https://php.net/manual/en/serializable.serialize.php)
     */
    public function serialize(): string
    {
        return serialize($this->toArray());
    }

    /**
     * Constructs the object
     * See [unserialize](https://php.net/manual/en/serializable.unserialize.php)
     */
    public function unserialize($serialized)
    {
        $serialized = (string) $serialized;
        $data       = unserialize($serialized);

        $this->init($data);
    }

    /**
     * Returns the object in an array format
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Returns the object in a JSON format
     *
     * The default string uses the following options for json_encode
     *
     * `JSON_HEX_TAG`, `JSON_HEX_APOS`, `JSON_HEX_AMP`, `JSON_HEX_QUOT`,
     * `JSON_UNESCAPED_SLASHES`
     *
     * See [rfc4627](https://www.ietf.org/rfc/rfc4627.txt)
     */
    public function toJson(int $options = 79): string
    {
        return Json::encode($this->toArray(), $options);
    }

    /**
     * 保存数据（内部使用）
     */
    protected function setData(string $element, $value)
    {
        $key = $this->lowerElement($element);

        $this->data[$element]  = $value;
        $this->lowerKeys[$key] = $element;
    }

    /**
     * 将元素名转换为小写
     */
    protected function lowerElement(string $element)
    {
        return (true === $this->insensitive) ? strtolower($element) : $element;
    }
}
