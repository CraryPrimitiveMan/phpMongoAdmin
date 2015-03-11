<?php
namespace base\mongo;

use SplStack;

class PositionStack extends SplStack
{
    /**
     * Stack position, avoid to foreach every time
     * @var string
     */
    public $position = '';

    /**
     * Push a key into stack
     * @param mixed $key
     */
    public function push($key) {
        // Add the position
        if(empty($this->position)) {
            $this->position = $key;
        } else {
            $this->position .= '.' . $key;
        }
        parent::push($key);
    }

    /**
     * Pop a key from stack
     * @return mixed
     */
    public function pop() {
        $key = parent::pop();
        // Remove the position
        $this->position = str_replace($key, '', $this->position);

        if (!empty($this->position) && substr($this->position, -1, 1) === '.') {
            $this->position = rtrim($this->position, '.');
        }

        return $key;
    }
}