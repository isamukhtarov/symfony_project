<?php

declare(strict_types=1);

namespace Ria\Bundle\StatisticBundle\Component;

use Yectep\PhpSpreadsheetBundle\Factory;

class ExcelExport
{
    /**
     * @param Factory $factory
     * @param array $statisticData
     * @return string
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export(Factory $factory, array $statisticData): string
    {
        $spreadsheet = $factory->createSpreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Statistic');
        $columnsMap = [];

        $lineIndex = 2;
        foreach ($statisticData as $line) {
            foreach ($line as $columnName=>$columnValue) {
                if (is_int($columnIndex = array_search($columnName, $columnsMap))) {
                    $columnIndex++;
                } else {
                    $columnsMap[] = $columnName;
                    $columnIndex = count($columnsMap);
                }
                $sheet->getCellByColumnAndRow($columnIndex, $lineIndex)->setValue($columnValue);
            }
            $lineIndex++;
        }
        foreach ($columnsMap as $columnMapId=>$columnTitle) {
            $sheet->getCellByColumnAndRow($columnMapId+1, 1)->setValue($columnTitle);
        }
        $writer = $factory->createWriter($spreadsheet, 'Xlsx');
        ob_start();
        $writer->save('php://output');

        return ob_get_clean();
    }
}