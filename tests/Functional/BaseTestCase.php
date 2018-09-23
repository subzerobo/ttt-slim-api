<?php

namespace Tests\Functional;

use App\GameEngine\State;
use App\GameEngine\TttBoard;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Environment;

/**
 * This is an example class that shows how you could set up a method that
 * runs the application. Note that it doesn't cover all use-cases and is
 * tuned to the specifics of this skeleton app, so if your needs are
 * different, you'll need to change it.
 */
class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Use middleware when running application?
     *
     * @var bool
     */
    protected $withMiddleware = true;

    /**
     * Process the application given a request method and URI
     *
     * @param $requestMethod
     * @param $requestUri
     * @param null $requestData
     * @return \Psr\Http\Message\ResponseInterface|Response
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function runApp($requestMethod, $requestUri, $requestData = null)
    {
        // Create a mock environment for testing with
        $environment = Environment::mock(
            [
                'REQUEST_METHOD' => $requestMethod,
                'REQUEST_URI' => $requestUri
            ]
        );

        // Set up a request object based on the environment
        $request = Request::createFromEnvironment($environment);

        // Add request data, if it exists
        if (isset($requestData)) {
            $request = $request->withParsedBody($requestData);
        }

        // Set up a response object
        $response = new Response();

        // Use the application settings
        $settings = require __DIR__ . '/../../bootstrap/settings.php';

        // Instantiate the application
        $app = new App($settings);

        // Set up dependencies
        require __DIR__ . '/../../bootstrap/dependencies.php';

        // Register middleware
        if ($this->withMiddleware) {
            require __DIR__ . '/../../bootstrap/middleware.php';
        }

        // Register routes
        require __DIR__ . '/../../bootstrap/routes.php';

        // Process the application
        $response = $app->process($request, $response);

        // Return the response
        return $response;
    }

    protected function buildLayout(array $layoutTypes): array
    {
        $rows = count($layoutTypes);
        $columns = count($layoutTypes[0]);
        $layout = [];
        for ($row = 0; $row < $rows; $row++) {
            for ($column = 0; $column < $columns; $column++) {
                $layout[$row][$column]['type'] = $layoutTypes[$row][$column];
            }
        }

        return $layout;
    }

    protected function createNewState(array $layoutTypes, int $player = TttBoard::CELL_X): State
    {
        $board = new TttBoard();
        $board->setLayout($this->buildLayout($layoutTypes));

        return new State($board, $player);
    }

    protected function getTypeAtPosition(array $layout, array $position): int
    {
        return $layout[$position[0]][$position[1]]['type'];
    }
}
