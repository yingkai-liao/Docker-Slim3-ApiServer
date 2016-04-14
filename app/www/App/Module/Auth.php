<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Module;
use App\Core\DynamoDB;

class Auth
{   
    public static function GenerateToken()
    {//todo: 加入mac address之類，確保不同機器不會重複
        return $token = bin2hex(openssl_random_pseudo_bytes(16));
    }
        
    public static function GenerateGUID()
    {//todo: 加入mac address之類，確保不同機器不會重複
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = uniqid(rand(), false);
        return $charid;
    }
    
    public static function SetAccessTokenWithPlayerId($playerId,$db_playerToToken,$newAccessToken,DynamoDB $DynamoDB)
    {           
        $tokenToPlayer = array(
            "playerId" => $playerId,
            "expired" => time() + 60 * 20,
        );
        $playerToToken = array(
          "accessToken" => $newAccessToken
        );
        if(isset($db_playerToToken))
        {
            $DynamoDB->BatchDelete(DBTable::$TokenToPlayer, $db_playerToToken["accessToken"]);
        }
        $DynamoDB->BatchPut(DBTable::$TokenToPlayer, $newAccessToken,$tokenToPlayer);
        $DynamoDB->BatchPut(DBTable::$PlayerToToken, $playerId,$playerToToken);
    }
            
    public static function GetPlayerIdFromAccessToken($db_tokenToPlayer) 
    {        
        if(isset($db_tokenToPlayer))
        {            
            $playerId = $db_tokenToPlayer["playerId"];
            $expired = $db_tokenToPlayer["expired"];
            if($expired > time())
            {
                return $playerId;
            }
        }
        
        return null;        
    }
    
    public static function isAccessTokenExpired($db_accessToken)
    {
        if(isset($db_accessToken))
        {
            $expired = $db_accessToken["expired"];
            return $expired < time();
        }
        return false;
    }
    
    public static function isAccessTokenExist($db_accessToken)
    {
        return isset($db_accessToken);
    }

    public static function CheckUUID($db_player,$uuid) 
    {
        if(isset($db_player))
             return $uuid === $db_player["uuid"];
        return false;
    }

    /*
    static public function getGUID()
    {
        if (function_exists('com_create_guid')){
            return com_create_guid();
        }else{
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = chr(123)// "{"
                .substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12)
                .chr(125);// "}"
            return $uuid;
        }
    }*/
}