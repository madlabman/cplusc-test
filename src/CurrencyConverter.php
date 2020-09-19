<?php

namespace App;

interface CurrencyConverter
{
    /**
     * Returns EUR equivalent of the given amount
     *
     * @param float $amount
     * @param string $currency_code
     * @return float
     * @throws \Exception
     */
    public function get_amount_in_eur(float $amount, string $currency_code);
}