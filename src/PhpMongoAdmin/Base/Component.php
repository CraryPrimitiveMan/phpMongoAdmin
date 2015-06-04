<?php
namespace PhpMongoAdmin\Base;

/**
 * Class Component is the base class that implements the init function.
 * @package PhpMongoAdmin\Base
 */
class Component
{
    /**
     * Construct function
     */
    public function __construct()
    {
        call_user_func_array([$this, 'init'], (array) func_get_args());
    }

    /**
     * Init component
     * @return bool
     */
    public function init()
    {
        return true;
    }
}
