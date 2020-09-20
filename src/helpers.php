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
 * Returns parsed response from external API
 *
 * @param $uri
 * @return mixed
 * @throws Exception
 */
function api_request($uri)
{
    $result = file_get_contents($uri);
    if (!empty($result)) {
        return json_decode($result, true);
    }
    throw new Exception('Unable to make request' . PHP_EOL);
}

/**
 * Round fee value by cents
 *
 * @param float $amount
 * @return float
 */
function round_fee_by_cents(float $amount)
{
    // Set acceptable scale value
    $prev_scale = bcscale(64);
    // Round value
    $amount_rounded = round(abs($amount), 2);
    // Compare amounts, rounded should be greater, otherwise add cent
    if (bccomp($amount_rounded, abs($amount)) == -1 ) {
        $amount_rounded = bcadd($amount_rounded, 0.01);
    }
    // Change back scale
    bcscale($prev_scale);

    return $amount_rounded * ($amount > 0 ?: -1);
}