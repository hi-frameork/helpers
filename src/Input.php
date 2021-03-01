<?php declare(strict_types=1);

namespace Hi\Helpers;

use Hi\Helpers\Exceptions\ParameterCompareException;
use Hi\Helpers\Exceptions\ParameterRequiredException;
use Hi\Helpers\Exceptions\ParameterTypeException;
use Hi\Helpers\Exceptions\RuntimeException;
use function is_int;
use function is_numeric;
use function is_bool;
use function is_float;
use function is_string;
use function in_array;
use function strpos;
use function array_key_exists;
use function count;
use function func_get_args;
use function strlen;
use function is_date;
use function is_datetime;
use function is_timestamp;

/**
 * 输入参数对象
 * 用来包装参数以获取获取符合预期地数据
 * 可用于快速获取符合预期类型地参数值
 *
 * 使用示例：
 * <?php
 *  $input = new Input(['foo' => 'bar', 'see' => '1']);
 *  $input->string('foo');  // 返回(string 类型) 'bar'
 *  $input->int('see'); 返回(int 类型) 1
 *
 *  $input->int('foo') ; // 将会抛出异常
 */
class Input
{
    /**
     * 数据容器
     *
     * @var array<string, mixed>
     */
    protected array $data;

    /**
     * constructor
     * 为 input 注入数据体
     *
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * 简单数据对比
     *
     * @param mixed $value
     * @param array<int, mixed> $rule
     */
    protected function compare(string $key, $value, array $rule): bool
    {
        if (count($rule) < 2) {
            throw new RuntimeException(
                "\$rules 参数错误，格式示例： ['==', 1] ",
                StatusCode::E_500000,
                func_get_args()
            );
        }

        $expect = true;
        switch ($rule[0]) {
            case '>':
                $expect = $value > $rule[1];
                break;

            case '>=':
                $expect = $value >= $rule[1];
                break;

            case '<':
                $expect = $value < $rule[1];
                break;

            case '<=':
                $expect = $value <= $rule[1];
                break;

            case '==':
                $expect = $value == $rule[1];
                break;

            case '===':
                $expect = $value === $rule[1];
                break;

            case 'in':
                $expect = in_array($value, $rule[1]);
                break;

            default:
                throw new RuntimeException(
                    "只支持'>', '>=', '<', '<=', '==', '===', 'in' 操作",
                    StatusCode::E_500000,
                    func_get_args()
                );
        }

        if ($expect) {
            return true;
        }

        throw new ParameterCompareException(
            "key[{$key}] 参数比对失败",
            StatusCode::E_400000,
            func_get_args()
        );
    }

    /**
     * 指定 key 是否存在
     */
    public function has(string $key): bool
    {
        if (! $this->exist($key)) {
            return false;
        }

        $value = $this->data[$key] ?? null;

        // 需要判断 '0', '0.0' 这类情况
        // 在 PHP 种 '0' 与 '0.0' 被认为是空，即 false
        if (is_string($value)) {
            return strlen($value) ? true : false;
        }

        if (is_numeric($value)) {
            return true;
        }

        if (is_bool($value)) {
            return true;
        }

        return (bool) $value;
    }

    /**
     * 返回指定 key 是否存在数据
     */
    public function exist(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * 返回 dta 中 key 对应数据
     *
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, bool $required = false, $default = null)
    {
        if ($this->has($key)) {
            return $this->data[$key];
        }

        if ($required) {
            throw new ParameterRequiredException("参数 key[{$key}] 不能为空", StatusCode::E_400000);
        }

        if ($this->exist($key)) {
            return $this->data[$key];
        } else {
            return $default;
        }
    }

    /**
     * 检查并返回 key 为整型
     *
     * 参数只接受整型数字或者整型字符串，如：123, '234'
     * 对于 '123.45' 这种数字字符串将被视为 int 类型
     *
     * @param int $default
     * @param array<int, mixed> $rule
     */
    public function int(string $key, bool $required = false, $default = 0, array $rule = []): int
    {
        $value = $this->get($key, $required, $default);

        if ($rule) {
            $this->compare($key, $value, $rule);
        }

        if (is_int($value)) {
            return $value;
        }

        // 处理 value 值为数字字符串情况
        /** @var string $value */
        if (is_numeric($value) && strpos($value, '.') === false) {
            return (int) $value;
        }

        throw new ParameterTypeException("`{$key}` 值必须为 int 类型", StatusCode::E_400000, $value);
    }

    /**
     * 检查并返回 key 为浮点类型
     * 方法只接受处理纯浮点型或浮点型字符串
     *
     * @param float $default
     * @param array<int, mixed> $rule
     */
    public function float(string $key, bool $required = false, $default = 0.0, array $rule = []): float
    {
        $value = $this->get($key, $required, $default);

        if ($rule) {
            $this->compare($key, $value, $rule);
        }

        if (is_float($value)) {
            return $value;
        }

        if (is_numeric($value) && strpos((string) $value, '.') !== false) {
            return (float) $value;
        }

        throw new ParameterTypeException("`{$key}` 值必须为 float 类型", StatusCode::E_400000, $value);
    }

    /**
     * 检查并返回 key 为浮点类型
     *
     * @param bool $default
     * @param array<int, mixed> $rule
     */
    public function bool(string $key, bool $required = false, $default = false, array $rule = []): bool
    {
        $value = $this->get($key, $required, $default);

        if ($rule) {
            $this->compare($key, $value, $rule);
        }

        if (is_bool($value)) {
            return (bool) $value;
        }

        throw new ParameterTypeException("`{$key}` 值必须为 bool 类型", StatusCode::E_400000, $value);
    }

    /**
     * 检查并返回 key 为字符串
     *
     * @param string $default
     * @param array<int, mixed> $rule
     */
    public function string(string $key, bool $required = false, $default = '', array $rule = []): string
    {
        $value = $this->get($key, $required, $default);

        if ($rule) {
            $this->compare($key, $value, $rule);
        }

        if (is_string($value)) {
            return (string) $value;
        }

        throw new ParameterTypeException("`{$key}` 值必须为 string 类型", StatusCode::E_400000, $value);
    }

    /**
     * 检查并返回 key 为字 date 类型
     *
     * @param string $default
     * @param array<int, mixed> $rule
     */
    public function date(string $key, bool $required = false, $default = '', array $rule = []): string
    {
        $value = $this->string($key, $required, $default, $rule);

        if (is_date($value)) {
            return $value;
        }

        throw new ParameterTypeException(
            "`{$key}` 值必须为合法 date 类型，例：2000-01-01",
            StatusCode::E_400000,
            $value
        );
    }

    /**
     * 检查并返回 key 为字 datetime 类型
     *
     * @param string $default
     * @param array<int, mixed> $rule
     */
    public function datetime(string $key, bool $required = false, $default = '', array $rule = []): string
    {
        $value = $this->string($key, $required, $default, $rule);

        if (is_datetime($value)) {
            return $value;
        }

        throw new ParameterTypeException(
            "`{$key}` 值必须为合法 datetime 类型，例：2000-01-01 00:00:00",
            StatusCode::E_400000,
            $value
        );
    }

    /**
     * 检查并返回 key 为字 timestamp 类型
     *
     * @param int $default
     * @param array<int, mixed> $rule
     */
    public function timestamp(string $key, bool $required = false, $default = 0, array $rule = []): int
    {
        $value = $this->int($key, $required, $default, $rule);

        if (is_timestamp($value)) {
            return (int) $value;
        }

        throw new ParameterTypeException(
            "`{$key}` 值必须为合法 timestamp 类型，例：1611150603",
            StatusCode::E_400000,
            $value
        );
    }

    /**
     * 检查并返回 key 为字 timestamp 类型
     *
     * @param array<mixed> $default
     * @param array<int, mixed> $rule
     * @return array<mixed>
     */
    public function array(string $key, bool $required = false, $default = [], array $rule = []): array
    {
        $value = $this->get($key, $required, $default);

        if ($rule) {
            $this->compare($key, $value, $rule);
        }

        if (\is_array($value)) {
            return $value;
        }

        throw new ParameterTypeException("`{$key}` 值必须为 array 类型", StatusCode::E_400000, $value);
    }

    /**
     * 以 key 值为数据体，返回 Input 示例
     */
    public function object(string $key): Input
    {
        return new Input($this->array($key, true));
    }

    /**
     * 返回 $data 数据
     * 
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * 返回 json 格式数据
     *
     * @return string|false
     */
    public function toJson()
    {
        return Json::encode($this->data, JSON_UNESCAPED_UNICODE);
    }
}

