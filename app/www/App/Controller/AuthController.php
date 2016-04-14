<?php
namespace App\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\Controller;
use App\Core\CommonException;

use App\Module\Auth;
use App\Module\Player;
use App\Core\DynamoDB;
use App\Module\DBTable;


class AuthController extends Controller
{
    public function login (Request $request,Response $response ,Array $args) 
    {                       
        $paramKeys = array("playerId","uuid");
        $params = $this->ValidateParams($request,$paramKeys);        
        extract( $params, EXTR_SKIP );
        
        $DynamoDB = new DynamoDB();
        $data = $DynamoDB->BatchGetBegin()
                ->BatchGet(DBTable::$Player, $playerId)
                ->BatchGet(DBTable::$PlayerToToken, $playerId)
                ->BatchGetRun();
        
        $db_player = $data[DBTable::$Player][$playerId];
        $db_playerToToken = $data[DBTable::$PlayerToToken][$playerId];
        
        if(!isset($db_player))
        {
            throw new CommonException(401,"LoginError","PlayerNotExists");
        }
        if(!Auth::CheckUUID($db_player,$uuid))
        {
            throw new CommonException(401,"LoginError","UUIDNotMatch");
        }        
        
        $newAccessToken = Auth::GenerateToken();

        $DynamoDB->BatchWriteBegin();
        Auth::SetAccessTokenWithPlayerId($playerId, $db_playerToToken,$newAccessToken,$DynamoDB);
        $DynamoDB->BatchWriteRun();

         $result = array(
             "result" => true,
             "access_token" => $newAccessToken
         );        
         return $response->withJson($result);        
    }
    
    public function createPlayer (Request $request,Response $response ,Array $args) 
    {                       
        $paramKeys = array("uuid");
        $params = $this->ValidateParams($request,$paramKeys);        
        extract( $params, EXTR_SKIP );
        
        $playerId = Auth::GenerateGUID();        
        $accessToken = Auth::GenerateToken();
        
        $DynamoDB = new DynamoDB();
        $DynamoDB->BatchWriteBegin();
        Player::createNewPlayer($playerId,$uuid,$DynamoDB);
        Auth::SetAccessTokenWithPlayerId($playerId, null ,$accessToken,$DynamoDB);
        $DynamoDB->BatchWriteRun();
        
        $result = array(
            "result" => true,
            "access_token" => $accessToken,
            "playerId" => $playerId,
        );        

        return $response->withJson($result);
    }
        
    public function refresh_token (Request $request,Response $response ,Array $args) 
    {    
        $paramKeys = array("playerId","access_token");
        $params = $this->ValidateParams($request,$paramKeys);        
        extract( $params, EXTR_SKIP );

        $DynamoDB = new DynamoDB();        
        $db_playerToToken = $DynamoDB->getItem(DBTable::$PlayerToToken, $playerId);
        $newAccessToken = Auth::GenerateToken();        
        
        $DynamoDB->BatchWriteBegin();
        Auth::SetAccessTokenWithPlayerId($playerId,$db_playerToToken,$newAccessToken,$DynamoDB);
        $DynamoDB->BatchWriteRun();
        
        $result = array(
            "result" => true,
            "access_token" => $newAccessToken,
            "playerId" => $playerId,            
        );        
        
        return $response->withJson($result);
    }

}
