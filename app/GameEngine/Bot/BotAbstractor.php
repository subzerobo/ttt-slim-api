<?php declare(strict_types=1);

namespace App\GameEngine\Bot;

use App\GameEngine\State;

abstract class BotAbstractor implements BotInterface
{
    /**
     * @var State
     */
    protected $state;

    /**
     * {@inheritdoc}
     */
    public function takeTurn(State $state): State
    {
        // Once the game finished bot do not need to proceed
        if ($state->isGameFinished()) {
            return $state;
        }

        $this->state = $state;
        $newState = $state->move($this->computeMove());

        return $newState;
    }

    public function computeBotMove(State $state): array
    {
        if ($state->isGameFinished()) {
            return null;
        }
        $this->state = $state;
        if ($state->hasLastMove()) {
            return $state->hasLastMove();
        }
        return $this->computeBestMove();
    }

    /**
     * Get bot name
     *
     * @return string
     */
    abstract public function getName(): string;

    /**
     * Cell coordinates of the best move
     * Ex. [0,2]
     *
     * @var array
     *
     * @return array
     */
    abstract protected function computeBestMove(): array;

    /**
     * Cell coordinates of the next move
     * Ex. [0,2]
     *
     * @return array
     */
    private function computeMove(): array
    {
        if ($this->state->hasLastMove()) {
            return $this->state->hasLastMove();
        }

        return $this->computeBestMove();
    }
}
