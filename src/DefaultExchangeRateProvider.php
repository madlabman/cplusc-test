<?php


namespace App;


class DefaultExchangeRateProvider implements ExchangeRateProvider
{

    /**
     * Returns exchange rate for the currency
     *
     * @param $currency_code
     * @return mixed|null
     * @throws \Exception
     */
    public function get_exchange_rate_for_currency(string $currency_code)
    {
        // Base currency
        if ($currency_code == 'EUR') {
            return 0;
        }

        if (!empty($result = api_request('https://api.exchangeratesapi.io/latest'))
            && !empty($result['rates'][$currency_code])) {
            return $result['rates'][$currency_code];
        }

        return null;
    }
}