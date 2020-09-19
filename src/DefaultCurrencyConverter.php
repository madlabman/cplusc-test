<?php


namespace App;


class DefaultCurrencyConverter implements CurrencyConverter
{
    private ExchangeRateProvider $exchange_rate_provider;

    /**
     * CurrencyConverter constructor.
     *
     * @param ExchangeRateProvider $exchange_rate_provider
     */
    public function __construct(ExchangeRateProvider $exchange_rate_provider)
    {
        $this->exchange_rate_provider = $exchange_rate_provider;
    }

    /**
     * Returns EUR equivalent of the given amount
     *
     * @param float $amount
     * @param string $currency_code
     * @return float
     * @throws \Exception
     */
    public function get_amount_in_eur(float $amount, string $currency_code)
    {
        if (!is_numeric($amount)) {
            throw new \Exception('Non numeric amount value' . PHP_EOL);
        }

        $rate = $this->exchange_rate_provider->get_exchange_rate_for_currency($currency_code);
        if ($rate === null) {
            throw new \Exception('Unable to retrieve exchange rate' . PHP_EOL);
        }

        // Avoid divide by zero exception
        return $rate ? $amount / $rate : $amount;
    }
}