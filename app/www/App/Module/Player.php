<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Module;
use App\Core\DynamoDB;

class Player
{    
    public static function createNewPlayer($playerId,$uuid,DynamoDB $DynamoDB)
    {
        $newPlayer =array(
            "create-time" => time(),
            "uuid" =>$uuid
        );
        
        $DynamoDB->BatchPut(DBTable::$Player, $playerId, $newPlayer);
    }
}
 