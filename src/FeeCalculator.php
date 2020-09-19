<?php


namespace App;


class FeeCalculator
{
    protected static int $EU_PERCENT = 1;
    protected static int $NON_EU_PERCENT = 2;

    private BinProvider $bin_provider;
    private CurrencyConverter $currency_converter;

    /**
     * FeeCalculator constructor.
     *
     * @param BinProvider $bin_provider
     * @param CurrencyConverter $currency_converter
     */
    public function __construct(BinProvider $bin_provider, CurrencyConverter $currency_converter)
    {
        $this->bin_provider = $bin_provider;
        $this->currency_converter = $currency_converter;
    }

    /**
     * Returns fee value for the selected country and amount
     *
     * @param string $bin
     * @param string $amount
     * @param string $currency_code
     * @return float
     * @throws \Exception
     */
    public function calculate(string $bin, string $amount, string $currency_code)
    {
        // BIN lookup
        $country_code = $this->bin_provider->get_country_code_by_bin($bin);
        if (empty($country_code)) {
            throw new \Exception('Unable to parse country code from BIN lookup' . PHP_EOL);
        }
        // Calculate amount in EUR
        $eur_amount = $this->currency_converter->get_amount_in_eur($amount, $currency_code);
        // Calculate fee
        return ($eur_amount / 100) * (is_eu_code($country_code) ? self::$EU_PERCENT : self::$NON_EU_PERCENT);
    }
}