<?php declare(strict_types=1);

namespace Hi\Helpers;

/**
 * Expcetion 与 Error 错误 code 码定义处
 *
 * 规则为：
 *  前 3 位 与 HTTP 状态码对应，后三位为与业务对应地自定义值
 *
 * 示例：
 *  400000 表示 HTTP STATUS 为 400 的客户端参数异常
 *  500000 表示 HTTP STATUS 为 500 地系统服务异常
 */
class StatusCode
{
    /**
     * 参数异常
     */
    const E_400000 = 400000;

    /**
     * 系统异常
     */
    const E_500000 = 500000;
}
