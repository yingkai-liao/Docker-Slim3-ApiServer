<?php
namespace App\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\Controller;
use App\Core\CommonException;

use App\Module\GPS;

class VersionController
{        
    
    public function get_app_version (Request $request,Response $response ,Array $args) 
    {         
        $result = array(
            "version" => "0.1",
        );
        return $response->withJson($result);
    }
}