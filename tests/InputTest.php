<?php

namespace Hi\Tests;

use Hi\Helpers\Input;
use Hi\Helpers\StatusCode;
use PHPUnit\Framework\TestCase;

class InputTest extends TestCase
{
    protected Input $input;

    protected $data = [
        'int_1' => 0,
        'int_2' => 1,
        'int_3' => 'abc',
        'int_4' => '',
        'int_5' => [],
        'int_6' => [1, 2, 3],
        'int_7' => '990',
        'int_8' => '23.61'
    ];

    protected function setUp(): void
    {
        $this->input = new Input($this->data);
        parent::setUp();
    }

    public function testInit()
    {
        $this->assertSame($this->data, $this->input->toArray());
    }

    public function testInt()
    {
        $this->assertEquals(0, $this->input->int('int_1', true));
        $this->assertEquals(1, $this->input->int('int_2', true));
        $this->assertEquals(10, $this->input->int('int_10', false, 10));
        $this->assertEquals(990, $this->input->int('int_7', true));
        $this->assertEquals(23, $this->input->int('int_8', true));
    }

    public function testIntException()
    {
        $this->expectException(\Hi\Helpers\Exceptions\ParameterException::class);
        $this->expectExceptionCode(StatusCode::E_400000);

        $this->input->int(uniqid(), true);
        $this->input->int('int_3');
    }
}
