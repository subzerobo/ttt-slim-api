<?php declare(strict_types=1);

namespace App\GameEngine;

class State
{
    /**
     * @var TttBoard
     */
    private $board;

    /**
     * Winner at this state (if any)
     *  0 => no winner
     *  1 => Player X
     *  2 => Player O
     *
     * @var int|null
     */
    private $winner = 0;

    /**
     * Player whose turn in is this state
     *
     * @var int
     */
    private $player;

    public function __construct(TttBoard $board, int $player = TttBoard::CELL_X)
    {
        $this->board = $board;
        $this->player = $player;
        $this->checkForWin();
    }

    public function getLayout(): array
    {
        return $this->board->getLayout();
    }

    /**
     * Advance the position on the board
     *
     * @param array $movePosition Cell coordinates of the new board move ex. [0,2]
     *
     * @return State new state after the move
     */
    public function move(array $movePosition): State
    {
        $newBoard = clone $this->board;
        $newBoard->setCellType($this->getPlayer(), $movePosition[0], $movePosition[1]);

        return new State($newBoard, $this->getOppositePlayer());
    }

    /**
     * Check if this is last move and returns corresponding coordinates
     * Ex. [0,2]
     *
     * @return array|null
     */
    public function hasLastMove(): ?array
    {
        if ($this->board->getBlankCount() === 1) {
            return $this->getFirstAvailableMove();
        }

        return null;
    }

    /**
     * Get array of available moves in the current state
     *
     * @return array
     */
    public function getAvailableMoves(): array
    {
        return $this->board->getBlanks();
    }

    public function getPlayer(): int
    {
        return $this->player;
    }

    public function isGameFinished(): bool
    {
        return $this->hasWinner() || $this->isDraw();
    }

    public function hasWinner(): bool
    {
        return 0 !== $this->winner;
    }

    public function getWinner(): int
    {
        return $this->winner;
    }

    public function isDraw(): bool
    {
        return $this->board->isFullyOccupied() && !$this->hasWinner();
    }

    public function getScores() : array
    {
        return $this->board->getScores();
    }

    private function checkForWin(): void
    {
        $this->winner = $this->checkWinner() ?: 0;
    }

    private function checkWinner() : ?int{
        $score = $this->board->getScores();
        $girdSize = $this->board->getGridSize();

        for ($i = 0; $i < count($score); ++$i) {
            if ($score[$i] == $girdSize * TttBoard::PLAYER1_POINT) {
                return TttBoard::CELL_X;
            }

            if ($score[$i] == $girdSize * TttBoard::PLAYER2_POINT) {
                return TttBoard::CELL_O;
            }
        }

        return null;
    }

    /**
     * Returns coordinates of the first available move
     * Ex. [0,2]
     *
     * @return array|null
     */
    private function getFirstAvailableMove(): array
    {
        return array_pop(array_reverse($this->getAvailableMoves()));
    }

    private function getOppositePlayer(): int
    {
        return (TttBoard::CELL_X === $this->player) ? TttBoard::CELL_O : TttBoard::CELL_X;
    }
}
