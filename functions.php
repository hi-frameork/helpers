<?php

use Hi\Helpers\Json;

if (! function_exists('is_date')) {
    /**
     * 校验参数是否为合法date字符串
     * eg:
     *  2000-01-01
     *  2021/01/01
     */
    function is_date(string $date): bool
    {
        return \date('Y-m-d', \strtotime($date)) === $date;
    }
}

if (! function_exists('is_datetime')) {
    /**
     * 校验参数是否为合法datetime字符串
     * eg:
     *  2000-01-01 00:00:00
     *  2021/01/01 00:00:00
     */
    function is_datetime(string $datetime): bool
    {
        return \date('Y-m-d H:i:s', \strtotime($datetime)) === $datetime;
    }
}

if (! function_exists('is_timestamp')) {
    /**
     * 校验参数是否为合法datetime字符串
     * eg:
     *  1611150603
     */
    function is_timestamp($timestamp): bool
    {
        if ($timestamp < 0 || $timestamp > 2147454847) {
            return false;
        }

        return true;
    }
}

if (! function_exists('input')) {
    /**
     * 创建 input 实例
     *
     * @return \Hi\Helpers\Input
     */
    function input(array $data)
    {
        return new \Hi\Helpers\Input($data);
    }
}

if (! function_exists('objectToArray')) {
    /**
     * 将对象转换为数组
     *
     * @return array|false
     */
    function objectToArray($object)
    {
        return Json::decode(Json::encode($object), true);
    }
}
