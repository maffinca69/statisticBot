<?php


namespace App\Parsers;


class GoogleSpreadSheetParser implements ParserInterface
{

    /**
     * @param    array    $data
     * @return string
     */
    public function parse(array $data): string
    {
        $sheet = current($data['sheets']);

        $title = $sheet['properties']['title'] . PHP_EOL . PHP_EOL;

        $positionStatus = current($sheet['data'])['rowData'][2]['values'][1]['formattedValue'];
        $position = '🧑‍💻 ' . current($sheet['data'])['rowData'][1]['values'][1]['formattedValue'] . ' (' . $positionStatus . ')' . PHP_EOL;

        $rate = current($sheet['data'])['rowData'][6]['values'][1]['formattedValue'];

        $trackedValue = current($sheet['data'])['rowData'][9]['values'][1]['formattedValue'];
        $trackedType = plural_form((int)$trackedValue, ['час', 'часа', 'часов']);

        $salaryValue = current($sheet['data'])['rowData'][3]['values'][8]['formattedValue'];
        $preSalaryValue = ($rate * $trackedValue) < $salaryValue ? sprintf(' (%s)', $rate * $trackedValue) : ($rate . ' * ' . $trackedValue);
        $salary = '💸 ' . $salaryValue . PHP_EOL;
        $tracked = '⏱ ' . $trackedValue . ' ' . $trackedType . $preSalaryValue . PHP_EOL;

        $text = $title . $position . $tracked . $salary;

        // Statistic
        $text .= PHP_EOL;

        // План/оценка
        $planEstimate = current($sheet['data'])['rowData'][40]['values'];
        $planEstimateText = $planEstimate[0]['formattedValue'];
        $planEstimateValue = $planEstimate[1]['formattedValue'];
        $planEstimate = str_replace('- ', '', $planEstimateText) . ': ' . $planEstimateValue . PHP_EOL;

        // Оценка/трудозатраты
        $estimateTimeEntries = current($sheet['data'])['rowData'][41]['values'];
        $estimateTimeEntriesText = $estimateTimeEntries[0]['formattedValue'];
        $estimateTimeEntriesValue = $estimateTimeEntries[1]['formattedValue'];
        $estimateTimeEntries = str_replace('- ', '', $estimateTimeEntriesText) . ': ' . $estimateTimeEntriesValue . PHP_EOL;

        // План/трудозатраты
        $planTimeEntries = current($sheet['data'])['rowData'][42]['values'];
        $planTimeEntriesText = $planTimeEntries[0]['formattedValue'];
        $planTimeEntriesValue = $planTimeEntries[1]['formattedValue'];
        $planTimeEntries = str_replace('- ', '', $planTimeEntriesText) . ': ' . $planTimeEntriesValue . PHP_EOL;

        // Процент выработки
        $percent = current($sheet['data'])['rowData'][43]['values'];
        $percentText = $percent[0]['formattedValue'];
        $percentValue = $percent[1]['formattedValue'];
        $percent = str_replace('- ', '', $percentText) . ': ' . $percentValue . PHP_EOL;

        $text .= $planEstimate . $estimateTimeEntries . $planTimeEntries . $percent;

        return trim($text);
    }
}
