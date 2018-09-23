<?php declare(strict_types=1);

namespace App\Controllers;

use App\GameEngine\Bot\BotFactory;
use App\GameEngine\Exceptions\GameEngineException;
use App\GameEngine\State;
use App\GameEngine\TttBoard;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class GameController
{
    /**
     * Get new board
     *
     * HTTP GET /board
     *
     * @param RequestInterface $request
     * @param Response         $response
     *
     * @return ResponseInterface
     */
    public function init(RequestInterface $request, Response $response): ResponseInterface
    {
        $board = new TttBoard();
        return $response->withJson(['layout' => $board->getLayout()]);
    }

    /**
     * Play the move on the board by the user and return counter-move from the bot
     *
     * HTTP POST /move
     *
     * @param RequestInterface|Request $request
     * @param Response                 $response
     *
     * @return ResponseInterface
     */
    public function makeMove(Request $request, Response $response): ResponseInterface
    {
        try {
            $data = $request->getParsedBody();

            $board = new TttBoard();

            $board = $this->performPlayerMove(TttBoard::CELL_X,$board, $data['layout'], $data['position']);

            $bot = (new BotFactory())->createBot($data['botName']);
            $state = new State($board, TttBoard::CELL_O);
            $state = $bot->takeTurn($state);

            return $response->withJson([
                'layout' => $state->getLayout(),
                'status' => $this->getGameStatus($state),
                //'scores' => $state->getScores(),
            ]);
        }catch (GameEngineException $e){
            return $response->withJson([
                'message' => $e->getMessage(),
                'status' => 'error',
            ]);
        }catch (\TypeError $e)
        {
            return $response->withJson([
                'message' => "There is an input validation error, please check the details!",
                'details' => $e->getMessage(),
                'status' => 'error',
            ]);
        }

    }

    /**
     * Play the move on the board by the user or the move from the bot
     *
     * HTTP POST /move
     *
     * @param RequestInterface|Request $request
     * @param Response                 $response
     *
     * @return ResponseInterface
     */
    public function makeMoveManual(Request $request, Response $response): ResponseInterface
    {
        try {
            $data = $request->getParsedBody();

            $board = new TttBoard();

            $player_type = $data['player'];

            $board = $this->performPlayerMove($player_type, $board, $data['layout'], $data['position']);

            $state = new State($board, TttBoard::CELL_O);

            return $response->withJson([
                'layout' => $state->getLayout(),
                'status' => $this->getGameStatus($state),
            ]);

        }catch (GameEngineException $e){
            return $response->withJson([
                'message' => $e->getMessage(),
                'status' => 'error',
            ]);
        }catch (\TypeError $e)
        {
            return $response->withJson([
                'message' => "There is an input validation error, please check the details!",
                'details' => $e->getMessage(),
                'status' => 'error',
            ]);
        }

    }

    /**
     * Returns the Bot Move
     *
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface
     */
    public function askBotMove(Request $request, Response $response) : ResponseInterface
    {
        try{
            $data = $request->getParsedBody();
            $board = new TttBoard();
            $board->setLayout($data['layout']);

            $bot = (new BotFactory())->createBot($data['botName']);
            $state = new State($board, TttBoard::CELL_O);
            $bestMove = $bot->computeBotMove($state);

            return $response->withJson([
                'next_move' => $bestMove,
                'layout' => $state->getLayout(),
            ]);
        }
        catch (GameEngineException $e)
        {
            return $response->withJson([
            'message' => $e->getMessage(),
            'status' => 'error',
            ]);
        }
        catch (\TypeError $e)
        {
            return $response->withJson([
                'message' => "There is an input validation error, please check the details!",
                'details' => $e->getMessage(),
                'status' => 'error',
            ]);
        }
    }

    /**
     *  Place the players move on the board
     *
     * @param int $PlayerType
     * @param TttBoard $board
     * @param array $layout
     * @param array $movePosition
     * @return TttBoard
     */
    private function performPlayerMove(int $PlayerType, TttBoard $board, array $layout, array $movePosition): TttBoard
    {
        $board->setLayout($layout);
        $board->setCellType($PlayerType, $movePosition[0], $movePosition[1]);

        return $board;
    }

    /**
     * Returns the game status Array
     *
     * @param State $state
     * @return array
     */
    private function getGameStatus(State $state): array
    {
        $winner = $state->getWinner();
        if ($state->hasWinner()) {
            return $this->buildStatusMessage('win', $winner);
        }

        if ($state->isDraw()) {
            return $this->buildStatusMessage('draw', $winner);
        }

        return $this->buildStatusMessage('ongoing', $winner);
    }

    private function buildStatusMessage(string $stateStr, int $winner)
    {
        return ['state' => $stateStr, 'winner' => $winner];
    }
}
