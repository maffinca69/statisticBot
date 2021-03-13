<?php


namespace App\Parsers;


use Carbon\Carbon;

class GoogleDriveInfoFileParser implements ParserInterface
{

    /**
     * @param    array    $data
     * @return string
     */
    public function parse(array $data): string
    {
        // set timezone, because modifiedTime - RFC 3339 (UTC +0)
        $updatedAt = Carbon::parse($data['modifiedTime'])->setTimezone(config('app.timezone'));
        $updatedAt = sprintf('_Обновлено: %s_', $updatedAt->format('d.m.y H:m'));

        return $updatedAt;
    }
}
