<?php declare(strict_types=1);

namespace App\GameEngine\Bot;

use App\GameEngine\Exceptions\BotNotFoundException;

class BotFactory
{
    /**
     * Create new bot based on the bot type
     *
     * @param string $name
     *
     * @return BotInterface
     */
    public function createBot(string $name): BotInterface
    {
        switch ($name) {
            case GeniusBot::NAME:
                return new GeniusBot();
            case DumbBot::NAME:
                return new DumbBot();
        }

        throw new BotNotFoundException('Unsupported type of the bot "' . $name . '"');
    }
}
