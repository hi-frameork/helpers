<?php

namespace Hi\Tests;

use Hi\Helpers\Exception;
use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase
{
    protected $message = '错误说明';
    protected $code = 30000;
    protected $runtimeData = [__METHOD__, __DIR__];

    /**
     * @var Exception
     */
    protected $ex;

    protected function setUp(): void
    {
        $this->ex = new Exception($this->message, $this->code, $this->runtimeData);
    }
    public function testInit()
    {
        $this->assertInstanceOf(Exception::class, $this->ex);
        $this->assertEquals($this->message, $this->ex->getMessage());
        $this->assertEquals($this->code, $this->ex->getCode());
        $this->assertEquals($this->runtimeData, $this->ex->getRuntime());
    }
}

