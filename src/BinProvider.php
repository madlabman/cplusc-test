<?php


namespace App;


interface BinProvider
{
    /**
     * Returns country code by BIN
     *
     * @param $bin
     * @return mixed|null
     * @throws \Exception
     */
    public function get_country_code_by_bin(string $bin);
}