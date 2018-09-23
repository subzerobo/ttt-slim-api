<?php declare(strict_types=1);

namespace App\GameEngine\Bot;

/**
 * Does the moves randomly, no knowledge of the game
 */
class DumbBot extends BotAbstractor
{
    const NAME = 'dumb';

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * Computes the move randomly from the available moves
     *
     * {@inheritdoc}
     */
    protected function computeBestMove(): array
    {
        $availableMoves = $this->state->getAvailableMoves();

        return $availableMoves[array_rand($availableMoves)];
    }
}
