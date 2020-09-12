<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class AppTest extends TestCase

{
    use \phpmock\phpunit\PHPMock;


    /**
     * @dataProvider round_data_provider
     */
    function test_round_function($amount, $expected)
    {
        $this->assertSame($expected, round_fee_by_cents($amount));
    }

    function round_data_provider()
    {
        return [
            [0, 0.0],
            [0.46180, 0.47],
            [0.45, 0.45],
        ];
    }

    /**
     * @dataProvider fee_data_provider
     */
    function test_calculate_fee($amount, $country_code, $expected)
    {
        $this->assertSame($expected, calculate_fee($amount, $country_code));
    }

    function fee_data_provider()
    {
        return [
            [0.0, '', 0.0],
            [0.0, 'DE', 0.0],
            [100.0, '', 2.0],
            [100.0, 'DE', 1.0],
        ];
    }

    /**
     * @dataProvider currency_exchange_data_provider
     */
    function test_currency_exchange($amount, $rate, $expected)
    {
        $this->assertSame($expected, get_amount_in_eur($amount, $rate));
    }

    function currency_exchange_data_provider()
    {
        return [
            [0.0, 0.0, 0.0],
            [100.0, 0.0, 100.0],
            [400.0, 4.0, 100.0],
            [25.0, 0.25, 100.0],
        ];
    }

    /**
     * @dataProvider validate_entry_data_provider
     * @param $entry
     * @param $expected
     */
    function test_validate_entry($entry, $expected)
    {
        $this->assertSame($expected, validate_entry($entry));
    }

    function validate_entry_data_provider()
    {
        return [
            [
                [
                    'amount' => 0.0,
                    'bin' => '123',
                    'currency' => 'EUR'
                ],
                true
            ],
            [
                [
                    'amount' => null,
                    'bin' => '123',
                    'currency' => 'EUR'
                ],
                false
            ],
            [
                [
                    'bin' => '123',
                    'currency' => 'EUR'
                ],
                false
            ],
        ];
    }

    /**
     * @dataProvider parse_line_data_provider
     */
    function test_parse_line($line, $expected)
    {
        $this->assertSame($expected, parse_line($line));
    }

    function parse_line_data_provider()
    {
        return [
            [
                '{"bin":"45717360","amount":"100.00","currency":"EUR"}',
                [
                    'bin' => '45717360',
                    'amount' => '100.00',
                    'currency' => 'EUR'
                ]
            ],
        ];
    }

    /**
     * @dataProvider app_data_provider
     */
    function test_app($line, $country_code, $rate, $expected)
    {
        $bin_lookup_mock = $this->getFunctionMock(__NAMESPACE__, 'get_country_code_by_bin');
        $bin_lookup_mock->expects($this->once())->willReturn($country_code);
        $exchange_rate_mock = $this->getFunctionMock(__NAMESPACE__, 'get_exchange_rate_for_currency');
        $exchange_rate_mock->expects($this->once())->willReturn($rate);

        $entry = parse_line($line);
        $country_code = get_country_code_by_bin($entry['bin']);
        $rate = get_exchange_rate_for_currency($entry['currency']);
        $eur_amount = get_amount_in_eur($entry['amount'], $rate);
        $fee = calculate_fee($eur_amount, $country_code);
        $fee = round_fee_by_cents($fee);

        $this->assertSame($expected, $fee);
    }

    function app_data_provider()
    {
        return [
            ['{"bin":"516793","amount":"50.00","currency":"USD"}', 'RU', 0.5, 2.0],
            ['{"bin":"45717360","amount":"100.00","currency":"EUR"}', 'DE', 0, 1.0]
        ];
    }
}
