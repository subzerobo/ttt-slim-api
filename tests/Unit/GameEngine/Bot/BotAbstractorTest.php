<?php declare(strict_types=1);

namespace Tests\Unit\GameEngine\Bot;

use App\GameEngine\Bot\BotAbstractor;
use App\GameEngine\State;
use App\GameEngine\TttBoard;
use Tests\Unit\BaseTttTest;

class BotAbstractorTest extends BaseTttTest
{
    public function testTakeTurn()
    {
        /** @var BotAbstractor|\PHPUnit_Framework_MockObject_MockObject $botAbstractor */
        $botAbstractor = $this->getMockForAbstractClass(BotAbstractor::class);
        $botAbstractor->expects($this->any())
            ->method('computeBestMove')
            ->will($this->returnValue([2, 2]));

        $state = $this->createNewState([
            [0, 0, 2],
            [0, 1, 2],
            [1, 1, 0],
        ]);
        $newState = $botAbstractor->takeTurn($state);

        $this->assertInstanceOf(State::class, $newState);
        $this->assertEquals($this->createNewState([
            [0, 0, 2],
            [0, 1, 2],
            [1, 1, 1],
        ], TttBoard::CELL_O), $newState);
    }
}
