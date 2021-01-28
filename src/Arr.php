<?php

declare(strict_types=1);

namespace Hi\Helpers;

use function array_key_exists;
use function settype;

class Arr
{
    final public static function get(
        array $data,
        $key,
        $defaultValue = null,
        string $cast = null
    ) {
        if (! array_key_exists($key, $data)) {
            return $defaultValue;
        }

        $value = $data[$key];

        if ($cast) {
            settype($value, $cast);
        }

        return $value;
    }
}
