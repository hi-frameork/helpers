<?php declare(strict_types=1);

namespace Hi\Helpers;

use InvalidArgumentException;

use function json_decode;
use function json_last_error;
use function json_last_error_msg;
use function json_encode;

/**
 * 为 JSON 序列化与反序列化提供更好支持
 */
class Json
{
    /**
     * 使用 `json_decode` 对字符串进行 JSON 反序列化
     * 如果失败将会抛出异常
     *
     * ```php
     * use Hi\Helpers\Json;
     *
     * $data = '{"one":"two","0":"three"}';
     *
     * var_dump(Json::decode($data));
     * // [
     * //     'one' => 'two',
     * //     'three'
     * // ];
     * ```
     *
     * @return mixed
     *
     * @throws InvalidArgumentException 如果反序列化失败，将会抛出此异常
     * @link http://www.php.net/manual/en/function.json-decode.php
     */
    final public static function decode(
        string $data,
        bool $associative = false,
        int $depth = 512,
        int $options = 0
    ) {
        $decoded = json_decode($data, $associative, $depth, $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidArgumentException(
                "json_decode error: " . json_last_error_msg()
            );
        }

        return $decoded;
    }

    /**
     * 使用 `json_encode` 对数据进行 JSON 序列化
     * 如果序列化失败，将会抛出此异常
     *
     * ```php
     * use Hi\Helpers\Json;
     *
     * $data = [
     *     'one' => 'two',
     *     'three'
     * ];
     *
     * echo Json::encode($data);
     * // {"one":"two","0":"three"}
     * ```
     * @return mixed
     *
     * @throws \InvalidArgumentException if the JSON cannot be encoded.
     * @link http://www.php.net/manual/en/function.json-encode.php
     */
    final public static function encode(
        $data,
        int $options = 0,
        int $depth = 512
    ): string {
        $encoded = json_encode($data, $options, $depth);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidArgumentException(
                "json_encode error: " . json_last_error_msg()
            );
        }

        return $encoded;
    }
}
