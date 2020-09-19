<?php


namespace App;


/**
 * @property string|null bin
 * @property string|null amount
 * @property string|null currency
 */
class Entry
{
    protected array $inner_data;

    protected array $allowed = ['bin', 'amount', 'currency'];

    /**
     * Entry constructor.
     *
     * @param array $entry_data
     */
    public function __construct(array $entry_data)
    {
        $this->inner_data = [];

        foreach ($entry_data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function __get($name)
    {
        if (isset($this->inner_data[$name])) {
            return $this->inner_data[$name];
        }

        return null;
    }

    public function __set($name, $value)
    {
        if (in_array($name, $this->allowed)) {
            $this->inner_data[$name] = $value;
        }
    }

    public function __isset($name)
    {
        return isset($this->inner_data[$name]);
    }
}