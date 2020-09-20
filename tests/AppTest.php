<?php

namespace Tests;


use App\BinProvider;
use App\CurrencyConverter;
use App\DefaultCurrencyConverter;
use App\DefaultEntryValidator;
use App\Entry;
use App\ExchangeRateProvider;
use App\FeeCalculator;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase

{
    /**
     * @dataProvider round_data_provider
     * @param $amount
     * @param $expected
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
            [0.46, 0.46],
            [0.452, 0.46],
            [0.45002, 0.46],
            [100.45, 100.45],
            [-0.452, -0.46],
            [-0.45002, -0.46],
            [-100.452, -100.46],
        ];
    }

    /**
     * @dataProvider fee_data_provider
     * @param $amount
     * @param $country_code
     * @param $expected
     * @throws \Exception
     */
    function test_calculate_fee($amount, $country_code, $expected)
    {
        $bin_stub = $this->createStub(BinProvider::class);
        $bin_stub->method('get_country_code_by_bin')
            ->willReturn($country_code);

        $currency_converter_stub = $this->createStub(CurrencyConverter::class);
        $currency_converter_stub->method('get_amount_in_eur')
            ->willReturn($amount);

        $fee_calculator = new FeeCalculator($bin_stub, $currency_converter_stub);
        $this->assertSame($expected, $fee_calculator->calculate('SOME_UNUSED_VALUE', $amount, $country_code));
    }

    function fee_data_provider()
    {
        return [
            [0.0, 'RU', 0.0],
            [0.0, 'DE', 0.0],
            [100.0, 'RU', 2.0],
            [100.0, 'DE', 1.0],
        ];
    }

    /**
     * @dataProvider currency_exchange_data_provider
     * @param $amount
     * @param $rate
     * @param $expected
     * @throws \Exception
     */
    function test_currency_exchange($amount, $rate, $expected)
    {
        $exchange_rate_stub = $this->createStub(ExchangeRateProvider::class);
        $exchange_rate_stub->method('get_exchange_rate_for_currency')
            ->willReturn($rate);

        $converter = new DefaultCurrencyConverter($exchange_rate_stub);

        $this->assertSame($expected, $converter->get_amount_in_eur($amount, 'SOME_UNUSED_VALUE'));
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
     * @dataProvider entry_data_provider
     */
    function test_entry($data)
    {
        $entry = new Entry($data);
        foreach ($data as $key => $value) {
            $this->assertSame($value, $entry->{$key});
        }
    }

    function entry_data_provider()
    {
        return [
            [
                [
                    'bin' => '45717360',
                    'amount' => '100.00',
                    'currency' => 'EUR'
                ]
            ],
        ];
    }

    /**
     * @dataProvider validate_entry_data_provider
     * @depends      test_entry
     * @param $data
     * @param $expected
     */
    function test_validate_entry($data, $expected)
    {
        $validator = new DefaultEntryValidator();
        $this->assertSame($expected, $validator->validate(new Entry($data)));
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
     * @dataProvider entry_with_not_allowed_field_data_provider
     * @depends      test_validate_entry
     * @param $data
     */
    function test_entry_with_not_allowed_field($data)
    {
        $entry = new Entry($data);
        foreach (array_keys($data) as $key) {
            $this->assertEmpty($entry->{$key});
        }
    }

    function entry_with_not_allowed_field_data_provider()
    {
        return [
            [
                [
                    'SOME_KEY' => 'SOME_VALUE'
                ]
            ]
        ];
    }
}
