<?php
namespace App\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Module\Auth;
use App\Module\DBTable;
use App\Core\CommonException;
use App\Core\DynamoDB;

class AuthMiddleware
{
    public function __invoke(Request $request,Response $response, $next)
    {        
        //get playerId from accessToken
        $access_token = $request->getParam("access_token");
        if(!isset($access_token))
        {
            throw new CommonException(401,"LoginError","Unauthorized_WithoutToken");
        }
        //echo $access_token;
        $DynamoDB = new DynamoDB();          
        $db_accessToken = $DynamoDB->getItem(DBTable::$TokenToPlayer,$access_token);

        $playerId = Auth::GetPlayerIdFromAccessToken($db_accessToken);
        
        if($playerId == null)
        {
            if(!Auth::isAccessTokenExist($db_accessToken))
            {
                 throw new CommonException(401,"LoginError","Unauthorized_TokenNotExist");
            }
            
            if(Auth::isAccessTokenExpired($db_accessToken))
            {
                throw new CommonException(401,"LoginError","Unauthorized_TokenExpired");
            }
            
            throw new CommonException(401,"LoginError","PlayerNotFoundWithAccessToken");
        }

        $query_params = $request->getQueryParams();        
        $query_params["playerId"] = $playerId;        
        
        $request = $request->withQueryParams($query_params);      

        $response = $next($request, $response);
        return $response;
    }
}