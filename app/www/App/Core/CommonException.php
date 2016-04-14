<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Core;

 
class CommonException extends \Exception
{
    public $code;
    public $message;
    public $type;
    
    public function __construct($code,$type,$message)
    {
        $this->code = $code;
        $this->type = $type;
        $this->message = $message;
    }
}
