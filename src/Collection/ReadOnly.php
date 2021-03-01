<?php declare(strict_types=1);

namespace Hi\Helpers\Collection;

use Hi\Helpers\Collection;
use Hi\Helpers\Exception;

class ReadOnly extends Collection
{
    /**
     * Delete the element from the collection
     */
    public function remove(string $element)
    {
        throw new Exception("The object is read only");
    }

    /**
     * Set an element in the collection
     */
    public function set(string $element, $value)
    {
        throw new Exception("The object is read only");
    }
}
