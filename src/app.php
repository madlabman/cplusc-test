<?php

require __DIR__ . '/../vendor/autoload.php';

if (!empty($argv[1]) && file_exists($argv[1])) {
    // Read file
    foreach (read_input_file($argv[1]) as $line) {
        // Parse line
        $entry = parse_line($line);
        // Validate entry
        if (!validate_entry($entry)) {
            echo 'Wrong entry is given' . PHP_EOL;
            continue;
        }
        // BIN lookup
        $country_code = get_country_code_by_bin($entry['bin']);
        if (empty($country_code)) {
            echo 'Unable to parse country code from BIN lookup' . PHP_EOL;
            continue;
        }
        // Get exchange rate based on EUR
        $rate = get_exchange_rate_for_currency($entry['currency']);
        if ($rate === null) {
            echo 'Unable to retrieve exchange rate' . PHP_EOL;
            continue;
        }
        // Calculate amount in EUR
        if (!is_numeric($entry['amount'])) {
            echo 'Non numeric amount value' . PHP_EOL;
            continue;
        }
        $eur_amount = get_amount_in_eur($entry['amount'], $rate);
        // Calculate fee
        $fee = calculate_fee($eur_amount, $country_code);
        $fee = round_fee_by_cents($fee);

        echo $fee . PHP_EOL;
    }
} else {
    echo 'File does not exist' . PHP_EOL;
}











