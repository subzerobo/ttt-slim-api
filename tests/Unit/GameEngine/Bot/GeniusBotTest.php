<?php declare(strict_types=1);

namespace Tests\Unit\GameEngine\Bot;

use App\GameEngine\Bot\GeniusBot;
use App\GameEngine\TttBoard;
use Tests\Unit\BaseTttTest;

class GeniusBotTest extends BaseTttTest
{
    public function testGetName()
    {
        $bot = new GeniusBot();
        $this->assertEquals(GeniusBot::NAME, $bot->getName());
    }

    public function testTakeTurn()
    {
        // Check that bot makes perfect choices
        $bot = new GeniusBot();
        $newState = $bot->takeTurn($this->createNewState([
            [1, 0, 0],
            [0, 0, 0],
            [0, 0, 0],
        ], TttBoard::CELL_O));

        $this->assertEquals(2, $this->getTypeAtPosition($newState->getLayout(), [1, 1]));

        $bot = new GeniusBot();
        $newState = $bot->takeTurn($this->createNewState([
            [0, 0, 0],
            [0, 1, 0],
            [0, 0, 0],
        ], TttBoard::CELL_O));

        $this->assertEquals(2, $this->getTypeAtPosition($newState->getLayout(), [0, 0]));
    }
}
