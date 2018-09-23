<?php declare(strict_types=1);

namespace Tests\Unit\GameEngine\Bot;

use App\GameEngine\Bot\BotFactory;
use App\GameEngine\Bot\DumbBot;
use App\GameEngine\Bot\GeniusBot;
use Tests\Unit\BaseTttTest;

class BotFactoryTest extends BaseTttTest
{
    public function testCreateBot()
    {
        $factory = new BotFactory();

        $this->assertInstanceOf(DumbBot::class, $factory->createBot(DumbBot::NAME));
        $this->assertInstanceOf(GeniusBot::class, $factory->createBot(GeniusBot::NAME));
    }

    /**
     * @expectedException \App\GameEngine\Exceptions\BotNotFoundException
     */
    public function testCreateBotUnsupported()
    {
        $factory = new BotFactory();
        $factory->createBot('notexistentbot');
    }
}
