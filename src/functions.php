<?php

/**
 * Check if country code belongs to EU zone
 *
 * @param $code
 * @return bool
 */
function is_eu_code($code)
{
    $EU_CODE_LIST = [
        'AT',
        'BE',
        'BG',
        'CY',
        'CZ',
        'DE',
        'DK',
        'EE',
        'ES',
        'FI',
        'FR',
        'GR',
        'HR',
        'HU',
        'IE',
        'IT',
        'LT',
        'LU',
        'LV',
        'MT',
        'NL',
        'PO',
        'PT',
        'RO',
        'SE',
        'SI',
        'SK',
    ];

    return in_array($code, $EU_CODE_LIST);
}

/**
 * Generator function used for obtaining lines from selected file
 *
 * @param $filename
 * @return Generator
 */
function read_input_file($filename)
{
    $handle = fopen($filename, 'r');
    while ($line = fgets($handle)) {
        yield $line;
    }
    if (!feof($handle)) {
        echo 'Unexpected end of file' . PHP_EOL;
    }
    fclose($handle);
}

/**
 * Function returns parsed JSON (lines are encoded json strings actually)
 *
 * @param $line
 * @return mixed
 */
function parse_line($line)
{
    return json_decode($line, true);
}

/**
 * Return true if all of the required fields are presented and not null
 *
 * @param $entry
 * @return bool
 */
function validate_entry($entry)
{
    $required_fields = ['bin', 'amount', 'currency'];

    foreach ($required_fields as $field) {
        if (!isset($entry[$field]))
            return false;
    }
    return true;
}

/**
 * Returns parsed response from external API
 *
 * @param $uri
 * @return mixed
 */
function api_request($uri)
{
    $result = file_get_contents($uri);
    if (!empty($result)) {
        return json_decode($result, true);
    }
    echo 'Unable to make request' . PHP_EOL;
    return null;
}

/**
 * Returns country code by BIN
 *
 * @param $bin
 * @return mixed|null
 */
function get_country_code_by_bin($bin)
{
    if (!empty($result = api_request("https://lookup.binlist.net/${bin}"))
        && !empty($result['country']['alpha2'])) {
        return $result['country']['alpha2'];
    }

    return null;
}

/**
 * Returns exchange rate for the currency
 *
 * @param $currency_code
 * @return mixed|null
 */
function get_exchange_rate_for_currency($currency_code)
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

/**
 * Returns EUR equivalent of the given amount
 *
 * @param $amount
 * @param $rate
 * @return float
 */
function get_amount_in_eur($amount, $rate) {
    // Avoid divide by zero exception
    return $rate ? $amount / $rate : $amount;
}

/**
 * Round fee value by cents
 *
 * @param $amount
 * @return float
 */
function round_fee_by_cents($amount)
{
    return ceil(round($amount, 4) * 100) / 100;
}


/**
 * Returns fee value for the selected country and amount
 *
 * @param $amount
 * @param $country_code
 * @return float
 */
function calculate_fee($amount, $country_code) {
    $EU_PERCENT     = 1;
    $NON_EU_PERCENT = 2;

    return ($amount / 100) * (is_eu_code($country_code) ? $EU_PERCENT : $NON_EU_PERCENT);
}