<?php
namespace App\Core;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Monolog\Logger;

class ErrorHandler  extends \Slim\Handlers\Error
{
     protected $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(Request $request, Response $response, \Exception $exception)
    {   
        if(get_class($exception) == "App\Core\CommonException")
        {
            $code = $exception->code;
            $type = $exception->type;
            $message = $exception->message;
            
            $RemoteAddress = new \App\Utility\RemoteAddress();
            $ip = $RemoteAddress->getIpAddress();
            $now = date("Y-m-d H:i:s");  

            $logData = array(
                "timestamp" => time(),
                "time" => $now,
                "ip" => $ip,
                "url" => $_SERVER['REQUEST_URI'],                        
                "param" => json_encode($request->getParams()),
                "type" => $type,
                "message" => $message
            );

            $this->logger->critical($message,$logData);
            
            return $response
                    ->withStatus($code)
                    ->write($message);            
        }
        else 
        {
            // Log the message
            $this->logger->critical($exception->getMessage());

            // create a JSON error string for the Response body
            $body = json_encode([
                'error' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            return $response
                    ->withStatus(500)
                    ->withHeader('Content-type', 'application/json')
                    ->write($body);
        }
    }
}