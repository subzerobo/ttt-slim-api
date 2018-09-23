<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 9/19/18
 * Time: 9:51 AM
 */

namespace Tests\Functional;
use App\GameEngine\BoardAbstractor;
use App\GameEngine\TttBoard;
use Slim\Http\Response;

class GameControllerTest extends BaseTestCase
{

    /**
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testInit200()
    {
        $response = $this->runApp('GET', '/init');
        $this->assertEquals($response->getStatusCode(), 200);
    }

    /**
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testInitContainsEmptyLayout()
    {
        $response = $this->getParsedContents($this->runApp('GET', '/init'));
        $this->assertCount(3, $response['layout']);

        // Check that initial layout is all empty
        $layout = $response['layout'];
        $rows = count($layout);
        for ($row = 0; $row < $rows; $row++) {
            for ($column = 0; $column < $rows; $column++) {
                $this->assertEquals(BoardAbstractor::CELL_BLANK, $layout[$row][$column]['type']);
            }
        }
    }

    /**
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testMove200()
    {
        $response = $this->requestInitialMove();
        $this->assertEquals($response->getStatusCode(), 200);
    }

    /**
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testMoveContainsBody()
    {
        $response = $this->getParsedContents($this->requestInitialMove());

        $this->assertArrayHasKey('layout', $response);
        $this->assertCount(3, $response['layout']);
        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('state', $response['status']);
        $this->assertArrayHasKey('winner', $response['status']);
        $this->assertInternalType('string', $response['status']['state']);
        $this->assertInternalType('int', $response['status']['winner']);
    }

    /**
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testMoveRegistersPlayersPosition()
    {
        $position = [0, 1];
        $response = $this->getParsedContents($this->requestMove([
            [1, 0, 0],
            [2, 1, 0],
            [2, 0, 0],
        ], $position));

        $this->assertEquals(TttBoard::CELL_X, $this->getTypeAtPosition($response['layout'], $position));
    }

    /**
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testMoveDumbBot()
    {
        $position = [0, 1];
        $response = $this->getParsedContents($this->requestMove([
            [1, 0, 0],
            [2, 1, 0],
            [2, 0, 0],
        ], $position));

        // We need to check all empty positions for the random counter-move
        $emptyPositions = [[0, 2], [1, 2], [2, 1], [2, 2]];
        foreach ($emptyPositions as $position) {
            $type = $this->getTypeAtPosition($response['layout'], $position);
            if (BoardAbstractor::CELL_BLANK !== $type) {
                $this->assertEquals(TttBoard::CELL_O, $type);
            }
        }
    }

    /**
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testMoveGeniusBot()
    {
        // When first player plays corner, perfect player
        // has to play the center as initial move
        $position = [0, 0];
        $response = $this->getParsedContents($this->requestMove([
            [0, 0, 0],
            [0, 0, 0],
            [0, 0, 0],
        ], $position, 'genius'));

        $this->assertEquals($this->buildLayout([
            [1, 0, 0],
            [0, 2, 0],
            [0, 0, 0],
        ]), $response['layout']);

        // When first player plays center, perfect player
        // has to play the corner as initial move
        $position = [1, 1];
        $response = $this->getParsedContents($this->requestMove([
            [0, 0, 0],
            [0, 0, 0],
            [0, 0, 0],
        ], $position, 'genius'));

        $this->assertEquals($this->buildLayout([
            [2, 0, 0],
            [0, 1, 0],
            [0, 0, 0],
        ]), $response['layout']);
    }

    public function testMoveStateOngoing()
    {
        $this->doTestMove([2, 1], 'ongoing', 0, [
            [1, 0, 0],
            [2, 1, 0],
            [2, 0, 0],
        ]);
    }

    public function testMoveStateDraw()
    {
        $this->doTestMove([2, 2], 'draw', 0, [
            [2, 1, 2],
            [2, 1, 1],
            [1, 2, 0],
        ]);
    }

    public function testMoveStateBotWin()
    {
        $this->doTestMove([2, 2], 'win', TttBoard::CELL_O, [
            [2, 1, 1],
            [2, 1, 1],
            [2, 2, 0],
        ]);
    }

    public function testMoveStateWinRow()
    {
        $this->doTestMove([2, 2], 'win', TttBoard::CELL_X, [
            [1, 1, 1],
            [2, 1, 2],
            [2, 2, 0],
        ]);
    }

    public function testMoveStateWinColumn()
    {
        $this->doTestMove([2, 0], 'win', TttBoard::CELL_X, [
            [1, 1, 1],
            [2, 2, 1],
            [0, 2, 1],
        ]);
    }

    public function testMoveStateWinTopRightLeftBottomDiagonal()
    {
        $this->doTestMove([2, 0], 'win', TttBoard::CELL_X, [
            [1, 1, 2],
            [2, 1, 1],
            [0, 2, 1],
        ]);
    }

    public function testMoveStateWinTopLeftRightBottomDiagonal()
    {
        $this->doTestMove([2, 2], 'win', TttBoard::CELL_X, [
            [2, 2, 1],
            [2, 1, 0],
            [1, 2, 0],
        ]);
    }

    public function testMove4X4()
    {
        $this->doTestMove([0, 0], 'win', TttBoard::CELL_X, [
            [0, 0, 1, 1],
            [2, 1, 1, 0],
            [1, 1, 2, 2],
            [1, 2, 2, 0],
        ]);
    }

    public function testMove5X5()
    {
        $this->doTestMove([0, 0], 'win', TttBoard::CELL_X, [
            [0, 0, 1, 1, 1],
            [2, 1, 1, 0, 0],
            [1, 1, 1, 2, 2],
            [1, 2, 1, 2, 0],
            [1, 2, 1, 0, 0],
        ]);
    }

    /**
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testMoveIncorrectInput500()
    {
        $response = $this->runApp('POST', '/move', []);
        $this->assertEquals($response->getStatusCode(), 200);
    }

    /**
     * @return Response
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    private function requestInitialMove(): Response
    {
        return $this->requestMove([
            [0, 0, 0],
            [0, 0, 0],
            [0, 0, 0],
        ]);
    }

    /**
     * @param array $layoutTypes
     * @param array $position
     * @param string $botName
     * @return Response
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    private function requestMove(array $layoutTypes, array $position = [0, 2], string $botName = 'dumb'): Response
    {
        return $this->runApp('POST','/move',[
            'position' => $position,
            'botName' => $botName,
            'layout' => $this->buildLayout($layoutTypes),
        ]);
    }

    /**
     * @param Response $response
     * @return array
     */
    private function getParsedContents(Response $response): array
    {
        return json_decode((string)$response->getBody(), true);
    }

    private function assertStatus(array $response, string $state, int $winner)
    {
        $status = $response['status'];
        $this->assertEquals($state, $status['state']);
        $this->assertEquals($winner, $status['winner']);
    }

    private function doTestMove(array $position, string $state, int $winner, array $layoutTypes)
    {
        $response = $this->getParsedContents($this->requestMove($layoutTypes, $position));

        $this->assertStatus($response, $state, $winner);
    }


}