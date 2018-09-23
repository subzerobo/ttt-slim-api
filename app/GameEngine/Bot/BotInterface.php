<?php declare(strict_types=1);

namespace App\GameEngine\Bot;

use App\GameEngine\State;

interface BotInterface
{
    /**
     * Advance the game by changing current state to the new one
     *
     * @param State $state
     *
     * @return State
     */
    public function takeTurn(State $state): State;

    /**
     * Returns the Best Move for the Bot but not making the move !
     *
     * @param State $state
     * @return array
     */
    public function computeBotMove(State $state): array ;
}
