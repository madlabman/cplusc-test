<?php


namespace App;


require __DIR__ . '/../vendor/autoload.php';

// Suppress warnings
error_reporting(E_ALL ^ E_WARNING);

if (!empty($argv[1])) {
    // Read file
    $file_reader = new FileReader($argv[1]);
    try {
        foreach ($file_reader->get_entries() as $entry_data) {
            // Get entry
            $entry = new Entry($entry_data);
            // Validate entry
            $entry_validator = new DefaultEntryValidator();
            if (!$entry_validator->validate($entry)) {
                echo 'Wrong entry is given' . PHP_EOL;
                continue;
            }
            // Calculate fee amount
            $fee_calculator = new FeeCalculator(new DefaultBinProvider(),
                                                new DefaultCurrencyConverter(new DefaultExchangeRateProvider()));
            try {
                $fee = $fee_calculator->calculate($entry->bin, $entry->amount, $entry->currency);
                // Round value
                $fee = round_fee_by_cents($fee);
                echo $fee . PHP_EOL;
            } catch (\Exception $exception) {
                echo 'Unable to calculate fee amount' . PHP_EOL;
                echo $exception->getMessage();
            }
        }
    } catch (\Exception $exception) {
        echo $exception->getMessage();
    }
} else {
    echo 'Usage: php app.php <filename>' . PHP_EOL;
}











