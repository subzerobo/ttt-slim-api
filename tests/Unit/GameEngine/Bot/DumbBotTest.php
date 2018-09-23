<?php declare(strict_types=1);

namespace Tests\Unit\GameEngine\Bot;

use App\GameEngine\BoardAbstractor;
use App\GameEngine\Bot\DumbBot;
use App\GameEngine\TttBoard;
use Tests\Unit\BaseTttTest;

class DumbBotTest extends BaseTttTest
{
    public function testGetName()
    {
        $bot = new DumbBot();
        $this->assertEquals(DumbBot::NAME, $bot->getName());
    }

    public function testTakeTurn()
    {
        // Check that bot takes turn randomly
        $bot = new DumbBot();
        $newState = $bot->takeTurn($this->createNewState([
            [1, 2, 0],
            [2, 1, 0],
            [2, 1, 0],
        ], TttBoard::CELL_O));


        // We need to check all empty positions for the random counter-move
        $emptyPositions = [[0, 2], [1, 2], [2, 2]];
        foreach ($emptyPositions as $position) {
            $type = $this->getTypeAtPosition($newState->getLayout(), $position);
            if (BoardAbstractor::CELL_BLANK !== $type) {
                $this->assertEquals(TttBoard::CELL_O, $type);
            }
        }
    }
}
