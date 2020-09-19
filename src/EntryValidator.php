<?php


namespace App;


interface EntryValidator
{
    public function validate(Entry $entry);
}