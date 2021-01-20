<?php

namespace Hi\Tests\Stubs;

use Hi\Helpers\Input;

class InputStub extends Input
{
    public function bridgeCompare(string $key, $value, array $rule)
    {
        $this->compare($key, $value, $rule);
    }
}
