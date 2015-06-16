<?php

namespace Icinga\Module\Director\CustomVariable;

use Icinga\Module\Director\IcingaConfig\IcingaConfigHelper as c;
use Countable;

class CustomVariableDictionary extends CustomVariable implements Countable
{
    public function equals(CustomVariable $var)
    {
        if (! $var instanceof CustomVariableDictionary) {
            return false;
        }

        $myKeys = $this->listKeys();
        $foreignKeys = $var->listKeys();
        if ($myKeys !== $foreignKeys) {
            return false;
        }

        foreach ($this->value as $key => $value) {
            if ($this->$key->differsFrom($value)->$key) {
                return false;
            }
        }

        return true;
    }

    public function setValue($value)
    {
        $new = array();

        foreach ($value as $key => $val) {
            $new[$key] = self::wantCustomVariable($key, $val);
        }

        // WTF?
        if ($this->value === $new) {
            return $this;
        }

        $this->value = $new;
        $this->setModified();

        return $this;
    }

    public function listKeys()
    {
        $keys = array_keys($this->value);
        ksort($keys);
        return $keys;
    }

    public function count()
    {
        return count($this->value);
    }

    public function __clone()
    {
        foreach ($this->value as $key => $value) {
            $this->value->$key = clone($value);
        }
    }

    public function __get($key)
    {
        // ...
    }

    public function toConfigString()
    {
        return c::renderDictionary($this->value);
    }
}