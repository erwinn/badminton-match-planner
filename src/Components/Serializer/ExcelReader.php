<?php

declare(strict_types=1);

namespace MatchPlanner\Components;

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelReader
{
    /**
     * @var \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    private $file;

    public function __construct(string $filepath)
    {
        $this->file = IOFactory::load(__DIR__ . '/../../../files/' . $filepath);
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function getPlayersFromFile(): array
    {
        $playerNames = [];
        $sheet = $this->file->getSheet(0);
        foreach ($sheet->getRowIterator(2) as $row) {
            foreach ($row->getCellIterator('A', 'A') as $cell) {
                if ($cell->getValue() === null) {
                    return $playerNames;
                }
                $playerNames[] = $cell->getValue();
            }
        }

        return $playerNames;
    }
}