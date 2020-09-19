<?php


namespace App;


class DefaultBinProvider implements BinProvider
{

    /**
     * Returns country code by BIN
     *
     * @param $bin
     * @return mixed|null
     * @throws \Exception
     */
    public function get_country_code_by_bin(string $bin)
    {
        if (!empty($result = api_request("https://lookup.binlist.net/${bin}"))
            && !empty($result['country']['alpha2'])) {
            return $result['country']['alpha2'];
        }

        return null;
    }
}