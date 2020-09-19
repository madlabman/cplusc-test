<?php


namespace App;


class DefaultEntryValidator implements EntryValidator
{
    protected array $required = ['bin', 'amount', 'currency'];

    public function validate(Entry $entry)
    {
        foreach ($this->required as $field) {
            if (!isset($entry->{$field}))
                return false;
        }

        return true;
    }
}