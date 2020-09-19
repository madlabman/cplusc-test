<?php


namespace App;


class FileReader
{
    private string $filename;

    /**
     * FileReader constructor.
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    /**
     * Function returns parsed JSON (lines are encoded json strings actually)
     *
     * @param $line
     * @return mixed
     */
    private function parse_line($line)
    {
        return json_decode($line, true);
    }

    /**
     * Generator function used for obtaining lines from selected file
     *
     * @return \Generator
     * @throws \Exception
     */
    function get_entries()
    {
        $handle = fopen($this->filename, 'r');

        if (!$handle) {
            throw new \Exception('Unable to open stream' . PHP_EOL);
        }

        while ($line = fgets($handle)) {
            yield $this->parse_line($line);
        }

        if (!feof($handle)) {
            throw new \Exception('Unexpected end of file' . PHP_EOL);
        }

        fclose($handle);
    }
}