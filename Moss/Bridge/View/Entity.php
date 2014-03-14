<?php

/*
 * This file is part of the Storage package
 *
 * (c) Michal Wachowski <wachowski.michal@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moss\Bridge\View;

/**
 * Moss entity abstract
 * Array access prototype to grants read-only access to entity properties
 *
 * @package Moss Bridge
 * @author  Michal Wachowski <wachowski.michal@gmail.com>
 */
abstract class Entity implements \ArrayAccess
{

    public function __construct($iArr = array())
    {
        foreach (get_object_vars($this) as $field => $value) {
            if (array_key_exists($field, $iArr)) {
                $this->$field = $iArr[$field];
            }
        }
    }

    /**
     * Whether a offset exists
     *
     * @param mixed $offset
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return property_exists($this, $offset);
    }

    /**
     * Offset to retrieve
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if (!isset($this->$offset)) {
            return null;
        }

        return $this->$offset;
    }

    /**
     * Offset to set
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }

    /**
     * Offset to unset
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        if (property_exists($this, $offset)) {
            unset($this->$offset);
        }
    }
}
