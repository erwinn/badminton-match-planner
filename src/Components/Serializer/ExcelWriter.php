<?php

declare(strict_types=1);

namespace MatchPlanner\Components;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelWriter
{
    /**
     * @var int
     */
    private $maxMatchesAmount;
    /**
     * @var \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    private $spreadsheet;

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->spreadsheet->removeSheetByIndex(0);
    }

    /**
     * @param int $maxAmount
     * @return $this
     */
    public function setMaxMatchAmount(int $maxAmount): self
    {
        $this->maxMatchesAmount = $maxAmount;
        return $this;
    }

    /**
     * @param array $players
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function saveMatchesToFile(array $players): void
    {
        for ($i = 1; $i < 4; $i++) {
            $sheet = $this->createSheet($players, $i);
            $this->spreadsheet->addSheet($sheet);
        }

        $writer = IOFactory::createWriter($this->spreadsheet, 'Xlsx');
        $writer->save(__DIR__ . '/../../../files/' . date('YdmHis') . '_generated_matches.xlsx');
    }

    private function createSheet(array $players, int $sheetNumber): Worksheet
    {
        $sheet = new Worksheet($this->spreadsheet, 'Wedstrijden ronde ' . $sheetNumber);
        $sheet->setCellValue('A2', 'Baan');
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);

        shuffle($players);

        $matchPlayers = array_chunk($players, 4);
        $row = 3;
        $court = 1;
        foreach ($matchPlayers as $matchPlayer) {
            $sheet->setCellValue('A' . $row, $court);
            $sheet->setCellValue('C' . $row, '-');
            if (array_key_exists(0, $matchPlayer)) {
                $sheet->setCellValue('B' . $row, $matchPlayer[0]);
            }
            if (array_key_exists(1, $matchPlayer)) {
                $sheet->setCellValue('D' . $row, $matchPlayer[1]);
            }
            $row++;
            if (array_key_exists(2, $matchPlayer)) {
                $sheet->setCellValue('B' . $row, $matchPlayer[2]);
            }
            if (array_key_exists(3, $matchPlayer)) {
                $sheet->setCellValue('D' . $row, $matchPlayer[3]);
            }
            $row += 2;
            $court++;
        }
        $sheet->calculateColumnWidths();
        return $sheet;
    }
}