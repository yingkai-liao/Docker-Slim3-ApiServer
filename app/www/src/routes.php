<?php
// Routes
//$app = new \Slim\App($settings);
 
$app->group('/auth',function  () 
{
    $this->post('/login','\App\Controller\AuthController:login');
    $this->post('/create_player','\App\Controller\AuthController:createPlayer');
    $this->post('/refresh_token','\App\Controller\AuthController:refresh_token')->add(new App\Middleware\AuthMiddleware());
});

$app->group("/version",function () 
{
    $this->any('/get_app_version','\App\Controller\VersionController:get_app_version');    
});

$app->get('/[{name}]', function ($request, $response, $args) 
{
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");
    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});