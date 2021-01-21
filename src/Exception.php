<?php declare(strict_types=1);

namespace Hi\Helpers;

class Exception extends \Exception
{
    /**
     * @var mixed
     */
    protected $runtime;

    /**
     * constructor
     * @param mixed $runtime
     */
    public function __construct(string $message = '', int $code = -1, $runtime = null)
    {
        $this->runtime = $runtime;
        parent::__construct($message, $code);
    }

    /**
     * 返回 runtime 数据
     * @return mixed
     */
    public function getRuntime()
    {
        return $this->runtime;
    }
}

