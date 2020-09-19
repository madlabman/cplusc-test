<?php


namespace App;


interface ExchangeRateProvider
{
    /**
     * Returns exchange rate for the currency
     *
     * @param $currency_code
     * @return mixed|null
     * @throws \Exception
     */
    public function get_exchange_rate_for_currency(string $currency_code);
}