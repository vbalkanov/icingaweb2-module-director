<?php

namespace Icinga\Module\Director\CustomVariable;

use Icinga\Exception\ProgrammingError;

abstract class CustomVariable
{
    protected $key;

    protected $value;

    protected $storedValue;

    protected $type;

    protected $modified = false;

    protected function __construct($key, $value = null)
    {
        $this->key = $key;
        $this->setValue($value);
    }

    public function is($type)
    {
        return $this->getType() === $type;
    }

    public function getType()
    {
        if ($this->type === null) {
            $parts = explode('\\', get_class($this));
            $class = end($parts);
            // strlen('CustomVariable') === 9
            $this->type = substr(end($parts), 9);
        }

        return $this->type;
    }

    public function getKey()
    {
        return $this->key;
    }

    abstract public function setValue($value);

    public function hasBeenModified()
    {
        return $this->modified;
    }

    public function setModified($modified = true)
    {
        $this->modified = $modified;
        if (! $this->modified) {
            $this->storedValue = clone($this->value);
        }

        return $this;
    }

    public function setUnmodified()
    {
        return $this->setModified(false);
    }

    abstract public function equals(CustomVariable $var);

    abstract public function toConfigString();

    public function differsFrom(CustomVariable $var)
    {
        return ! $this->equals($var);
    }

    public static function wantCustomVariable($key, $value)
    {
        if ($value instanceof CustomVariable) {
            return $value;
        }

        return self::create($key, $value);
    }

    public static function create($key, $value)
    {
        if (is_string($value)) {

            return new CustomVariableString($key, $value);

        } elseif (is_array($value)) {

            foreach (array_keys($value) as $key) {
                if (! is_int($key) || ctype_digit($key)) {
                    return new CustomVariableDictionary($key, $value);
                }
            }

            return new CustomVariableArray($key, array_values($value));

        } elseif (is_object($value)) {
            // TODO: check for specific class/stdClass/interface?
            return new CustomVariableDictionary($key, $value);

        } else {
            throw new ProgrammingError();
        }
    }

    public static function fromDbRow($row)
    {
        switch($row->format) {
            case 'string':
                return new CustomVariableString($row->varname, $row->varvalue);
            case 'json':
                return self::create($row->varname, json_decode($row->varvalue));
            case 'expression':
                throw new ProgrammingError(
                    'Icinga code expressions are not yet supported'
                );
            default:
                throw new ProgrammingError(
                    '%s is not a supported custom variable format',
                    $row->format
                );
        }
    }

    public function __toString()
    {
        try {
            return $this->toConfigString();
        } catch (Exception $e) {
            trigger_error($e);
            $previousHandler = set_exception_handler(function () {});
            restore_error_handler();
            call_user_func($previousHandler, $e);
            die();
        }
    }
}