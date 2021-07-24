<?php

declare(strict_types=1);

namespace MatchPlanner\Components;

class MatchGenerator
{
    /**
     * @var \MatchPlanner\Components\ExcelReader
     */
    private $excelReader;
    /**
     * @var \MatchPlanner\Components\ExcelWriter
     */
    private $excelWriter;
    /**
     * @var int
     */
    private $maxMatches;

    public function __construct(int $maxMatches)
    {
        $this->excelWriter = new ExcelWriter();
        $this->maxMatches = $maxMatches;
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function generateMatches(string $filepath): void
    {
        $this->setFilePath($filepath);
        $players = $this->excelReader->getPlayersFromFile();
        $players = $this->randomizeArray($players);

        $this->excelWriter
            ->setMaxMatchAmount($this->maxMatches)
            ->saveMatchesToFile($players);
    }

    /**
     * @param array $players
     * @return array
     */
    private function randomizeArray(array $players): array
    {
        shuffle($players);
        return $players;
    }

    /**
     * @param string $filepath
     */
    private function setFilePath(string $filepath): void
    {
        $this->excelReader = new ExcelReader($filepath);
    }
}