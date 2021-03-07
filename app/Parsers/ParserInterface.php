<?php


namespace App\Parsers;


interface ParserInterface
{
    /**
     * Return formatted value
     *
     * @param    array    $data
     * @return string
     */
    public function parse(array $data): string;
}
