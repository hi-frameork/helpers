# Hi 框架 Helper 组件

为 Hi 框架提供基础 Exception 与 输入(Input) 参数过滤处理。

**Exception 示例：**

通过 `getRuntime` 方法，触发异常时传入地参数可以被框架在运行时进行收集，用于快速 debug。
```php
$e = new \Hi\Exception('Why', 0, ['runtime data for debug']);
$e->getMessage();
$e->getCode();
$e->getRuntime();
```


**Input 示例：**

```php
$input = new \Hi\Input([
    'item_1' => 1,
    'item_2' => 'abc'
    'item_3' => [4, 5]
    'item_4' => '123'
]);

var_dump($input->int('item_1'));  // output: int(1)
var_dump($input->int('item_4'));  // output: int(123)

var_dump($input->string('item_2'));  // output: string(3) abc

```
