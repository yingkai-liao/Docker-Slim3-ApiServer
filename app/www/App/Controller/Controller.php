<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Controller;

use Slim\Http\Request;
use App\Core\CommonException;

class Controller
{    
    protected function ValidateParams(Request $request,array $paramKeys)
    {        
        $input = $request->getParams();
        if(count($input) == 0)
            $input = $request->getQueryParams();        
        
        $valid = array_flip($paramKeys);

        $diffKeysCount = count(array_diff_key($valid,$input));
        
        if($diffKeysCount > 0)
        {
            throw new CommonException(400,"ApiError","ParamsNotMatch");
        }
        return $input;
    }   
}