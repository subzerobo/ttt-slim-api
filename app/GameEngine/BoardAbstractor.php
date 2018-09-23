<?php declare(strict_types=1);

namespace App\GameEngine;

class BoardAbstractor
{
    /**
     * Stores the Score for each Player instances
     */
    const PLAYER1_POINT = 1;  // User
    const PLAYER2_POINT = -1; // Bot

    const CELL_BLANK = 0;

    /**
     * @var int
     */
    protected $rows;

    /**
     * @var int
     */
    protected $columns;

    /**
     * @var array
     */
    protected $layout;

    /**
     * @var int
     */
    protected $gridSize = 3;

    /**
     * @var array
     * Score array that tracks score for rows, cols and diagonals.
     * e.g. for 3x3 grid [row1, row2, row3, col1, col2, col3, diag1, diag2]
     * e.g. row1 index is 0 or col2 index is 3+0 = 3
     */
    protected $scores;

    public function __construct(int $rows = 3, int $columns = 3)
    {
        $this->rows = $rows;
        $this->columns = $columns;
        $this->scores = array_fill(0, 2 * $this->getGridSize() + 2, 0);
        $this->initBoard();
    }

    public function getRows(): int
    {
        return $this->rows;
    }

    public function getColumns(): int
    {
        return $this->columns;
    }

    public function getLayout(): array
    {
        return $this->layout;
    }

    public function getScores(): array
    {
        return $this->scores;
    }

    public function getGridSize() : int
    {
        return $this->gridSize;
    }

    public function setLayout(array $layout)
    {
        $this->layout = $layout;
    }

    /**
     * Fetch specific point type from the
     *  board cell based on row, col location
     *
     * @param int $row Fetching point row
     * @param int $col Fetching point column
     *
     * @return int|null Fetched value otherwise false if point does not exist
     */
    public function getCellType(int $row, int $col): ?int
    {
        if (isset($this->layout[$row][$col])) {
            return $this->layout[$row][$col]['type'];
        }

        return null;
    }

    /**
     * Set the type of cell on the board
     *  in the specific location
     *
     * @param int $type Type to be set
     * @param int $row Setting point row
     * @param int $col Setting point column
     */
    public function setCellType(int $type, int $row, int $col): void
    {
        $this->layout[$row][$col]['type'] = $type;
        $this->setScores($type, $row, $col);
    }

    /**
     * Initialize empty gaming board
     *  - Empty board is 2D array with
     *     all point type set to 0 (int)
     *  Example:  0|0|0
     *            0|0|0
     *            0|0|0
     */
    private function initBoard(): void
    {
        for ($row = 0; $row < $this->rows; $row++) {
            for ($column = 0; $column < $this->columns; $column++) {
                $this->setCellType(self::CELL_BLANK, $row, $column);
            }
        }
    }

    /**
     * Set Scores for each possible wining conditions
     * @param int $type
     * @param int $row
     * @param int $col
     */
    private function setScores(int $type, int $row, int $col) : void {
        $point = 0;
        if (TttBoard::CELL_X == $type) $point = self::PLAYER1_POINT;
        if (TttBoard::CELL_O == $type) $point = self::PLAYER2_POINT;
        $this->scores[$row] += $point; // + Score for Row
        $this->scores[$this->gridSize + $col] += $point;  // + Score for Col
        if ($row == $col) $this->scores[2*$this->gridSize] += $point; // + Score for Diagonal  if it is
        if ($this->gridSize - 1 - $col == $row) $this->scores[2*$this->gridSize + 1] += $point; // + Score for Anti-Diagonal if it is
    }
}
