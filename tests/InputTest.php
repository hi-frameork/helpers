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
        'int_3' => '0',
        'int_4' => '1',
        'string_1' => 'alksjdfoij323',
        'string_2' => '',
        'string_3' => '666',
        'string_4' => '12.3',
        'bool_1' => true,
        'bool_2' => false,
        'float_1' => 0.0,
        'float_2' => 1.1,
        'date_1' => '2020-01-01',
        'date_2' => '2020-13-56',
        'datetime_1' => '2000-01-01 00:00:00',
        'datetime_2' => '1919-01-01 00:90:00',
        'timestamp_1' => 1611217459,
        'array_1' => [34, 565, 13],
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

    public function testParameterRequiredException()
    {
        $random = uniqid();
        $this->expectException(\Hi\Helpers\Exceptions\ParameterRequiredException::class);
        $this->expectExceptionCode(StatusCode::E_400000);
        $this->expectExceptionMessage("参数 key[{$random}] 不能为空");
        $this->input->string($random, true);
    }

    public function testStringParameterRequiredException()
    {
        $key = 'string_2';
        $this->expectException(\Hi\Helpers\Exceptions\ParameterRequiredException::class);
        $this->expectExceptionCode(StatusCode::E_400000);
        $this->expectExceptionMessage("参数 key[{$key}] 不能为空");
        $this->input->string($key, true);
    }

    public function testReturnDefault()
    {
        $key = uniqid();
        $this->assertEquals(888, $this->input->int($key, false, 888));
        $this->assertEquals(9999.03, $this->input->float($key, false, 9999.03));
        $this->assertEquals(false, $this->input->bool($key, false, false));
        $this->assertEquals('a', $this->input->string($key, false, 'a'));
        $date = date('Y-m-d');
        $this->assertEquals($date, $this->input->date($key, false, $date));
        $datetime = date('Y-m-d H:i:s');
        $this->assertEquals($datetime, $this->input->datetime($key, false, $datetime));
        $timestamp = time();
        $this->assertEquals($timestamp, $this->input->timestamp($key, false, $timestamp));
        $this->assertEquals(['a', 'b'], $this->input->array($key, false, ['a', 'b']));
    }

    public function testInt()
    {
        $this->assertEquals(0, $this->input->int('int_1', true));
        $this->assertEquals(1, $this->input->int('int_2', true));
        $this->assertEquals(10, $this->input->int('int_10', false, 10));
        $this->assertEquals(666, $this->input->int('string_3', true));
    }

    public function testIntParameterTypeException()
    {
        $key = 'string_1';
        $this->expectException(\Hi\Helpers\Exceptions\ParameterTypeException::class);
        $this->expectExceptionCode(StatusCode::E_400000);
        $this->expectExceptionMessage("`{$key}` 值必须为 int 类型");
        $this->input->int($key);
    }

    public function testFloat()
    {
        $this->assertEquals(0.0, $this->input->float('float_1', true));
        $this->assertEquals(1.1, $this->input->float('float_2', true));
        $this->assertEquals(12.3, $this->input->float('string_4', true));
    }

    public function testFloatParameterTypeException()
    {
        $key = 'int_1';
        $this->expectException(\Hi\Helpers\Exceptions\ParameterTypeException::class);
        $this->expectExceptionCode(StatusCode::E_400000);
        $this->expectExceptionMessage("`{$key}` 值必须为 float 类型");
        $this->input->float($key);
    }

    public function testBool()
    {
        $this->assertEquals(true, $this->input->bool('bool_1', true));
        $this->assertEquals(false, $this->input->bool('bool_2', true));
    }

    public function testBoolParameterTypeException()
    {
        $key = 'int_1';
        $this->expectException(\Hi\Helpers\Exceptions\ParameterTypeException::class);
        $this->expectExceptionCode(StatusCode::E_400000);
        $this->expectExceptionMessage("`{$key}` 值必须为 bool 类型");
        $this->input->bool($key);
    }

    public function testString()
    {
        $this->assertEquals('alksjdfoij323', $this->input->string('string_1', true));
        $this->assertEquals('666', $this->input->string('string_3', true));
        $this->assertEquals('', $this->input->string('string_2'));
    }

    public function testStringParameterTypeException()
    {
        $key = 'int_1';
        $this->expectException(\Hi\Helpers\Exceptions\ParameterTypeException::class);
        $this->expectExceptionCode(StatusCode::E_400000);
        $this->expectExceptionMessage("`{$key}` 值必须为 string 类型");
        $this->input->string($key);
    }

    public function testDate()
    {
        $this->assertEquals('2020-01-01', $this->input->date('date_1', true));
    }

    public function testDateParameterTypeException()
    {
        $key = 'date_2';
        $this->expectException(\Hi\Helpers\Exceptions\ParameterTypeException::class);
        $this->expectExceptionCode(StatusCode::E_400000);
        $this->expectExceptionMessage("`{$key}` 值必须为合法 date 类型，例：2000-01-01");
        $this->input->date($key, true);
    }

    public function testDatetime()
    {
        $this->assertEquals('2000-01-01 00:00:00', $this->input->datetime('datetime_1', true));
    }

    public function testDatetimeTypeException()
    {
        $key = 'datetime_2';
        $this->expectException(\Hi\Helpers\Exceptions\ParameterTypeException::class);
        $this->expectExceptionCode(StatusCode::E_400000);
        $this->expectExceptionMessage("`{$key}` 值必须为合法 datetime 类型，例：2000-01-01 00:00:00");
        $this->input->datetime($key, true);
    }

    public function testTimestamp()
    {
        $this->assertEquals(1611217459, $this->input->timestamp('timestamp_1', true));
    }

    public function testArray()
    {
        $this->assertEquals([34, 565, 13], $this->input->array('array_1', true));
    }

    public function testArrayParameterTypeException()
    {
        $key = 'string_1';
        $this->expectException(\Hi\Helpers\Exceptions\ParameterTypeException::class);
        $this->expectExceptionCode(StatusCode::E_400000);
        $this->expectExceptionMessage("`{$key}` 值必须为 array 类型");
        $this->input->array($key, true);
    }
}
