<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

// API routes
$app->get('/init', 'App\Controllers\GameController:init');
$app->post('/move', 'App\Controllers\GameController:makeMove');

// Routes for Manual Bot mode
$app->post('/move_manual', 'App\Controllers\GameController:makeMoveManual');
$app->post('/ask', 'App\Controllers\GameController:askBotMove');

// View Routes
$app->get('/', function ($request, $response, $args) {
    return $this->view->render($response, 'template.twig', []);
})->setName('web_interface');