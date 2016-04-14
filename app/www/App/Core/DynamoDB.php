<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Core;
use Aws\DynamoDb\Marshaler;

class DynamoDB
{
    private static $dynamodb = null;
    private $batchGet = null;
    private $batchWrite = null;
    
    function __construct() 
    {   
        if(DynamoDB::$dynamodb == null)
        {
            $sdk = new \Aws\Sdk([
                'region'   => 'us-west-2',
                'version'  => 'latest',        
                'endpoint' => 'http://db:8000',

                'credentials' => array(
                    'key'    => 'YOUR_AWS_ACCESS_KEY_ID',
                    'secret' => 'YOUR_AWS_SECRET_ACCESS_KEY',
                )
            ]);

            DynamoDB::$dynamodb = $sdk->createDynamoDb();
        }
    }
    
    function getDB()
    {
        return DynamoDB::$dynamodb;
    }
    
    function doesTableExist($tableName)
    {
        try
        {
            $response = DynamoDB::$dynamodb->describeTable(array('TableName' => $tableName));
        } catch (\Aws\Exception\AwsException $e) {
            if($e)
            return false;
        }
        
        return true;
    }
    
    function createTable($tableName)
    {
        try 
        {
            $response = DynamoDB::$dynamodb->createTable([
                'TableName' => $tableName,
                'AttributeDefinitions' => [
                    [
                        'AttributeName' => 'Id',
                        'AttributeType' => 'S' 
                    ]
                ],
                'KeySchema' => [
                    [
                        'AttributeName' => 'Id',
                        'KeyType' => 'HASH'  //Partition key
                    ]
                ],
                 'ProvisionedThroughput' => [
                    'ReadCapacityUnits'    => 5, 
                    'WriteCapacityUnits' => 6
                ]
            ]);
            
            DynamoDB::$dynamodb->waitUntil('TableExists', [
                'TableName' => $tableName,
                '@waiter' => [
                    'delay'       => 5,
                    'maxAttempts' => 20
                ]
            ]);    
        } catch (DynamoDbException $e) {
            new CommonException(500,"DBError","CantCreateTable");
        }  
    }
    
    function deleteTable($tableName)
    {
        $result = DynamoDB::$dynamodb->deleteTable([
            'TableName' => $tableName
        ]);

        DynamoDB::$dynamodb->waitUntil('TableNotExists', [
            'TableName' => $tableName,
            '@waiter' => [
                'delay'       => 5,
                'maxAttempts' => 20
            ]
        ]);
    }
    
    function putItem($tableName,$key,$data)
    {
        if(!isset($key) || !isset($tableName) || !isset($data))
            return null;
        
        $marshaler = new Marshaler();
        $data['Id'] = $key;
        
        $response  = DynamoDB::$dynamodb->putItem([
            'TableName' => $tableName,
            'Item'      => $marshaler->marshalItem($data)
        ]); 
        return $response;
    }
    
    function getItem($tableName,$key)
    {
        if(!isset($key) || !isset($tableName))
            return null;
        
        $marshaler = new Marshaler();
        
        $result = DynamoDB::$dynamodb->getItem([
            'TableName' => $tableName,
            'Key'       => ['Id' => ['S' => $key]]
        ]);
        
        $data = array();
        if(count($result['Item']) > 0)
            $data = $marshaler->unmarshalItem($result['Item']);
        
        return $data;
    }
    
    function BatchGetBegin()
    {
        $this->batchGet = array(
            'RequestItems' => [
            ]            
        );
        return $this;
    }
    
    function BatchWriteBegin()
    {
        $this->batchWrite = array(
            'RequestItems' => [
            ]            
        );
        return $this;
    }
    
    function BatchGet($tableName,$key)
    {
        if(!isset($key) || !isset($tableName))
            return null;
        
        if($this->batchGet == null)
        {
            throw  new CommonException(500,"DBApiError","ErrorBatchCommand");
        }
        
        if(array_key_exists ($tableName,$this->batchGet['RequestItems']))
        {
            $this->batchGet['RequestItems'][$tableName]['Keys'][] = array( 'Id'=> [ 'S' => $key ]);
        }
        else
        {
            $this->batchGet['RequestItems'][$tableName] = array(
                'Keys' =>[array( 'Id'=> [ 'S' => $key ])]
            );
        }
        return $this;
    }
    
    function BatchGetRun()
    {
        $marshaler = new Marshaler();
        $response = DynamoDB::$dynamodb->batchGetItem($this->batchGet);
        $this->batchGet = null;
        
        $data = array();

        foreach($response['Responses'] as $tableName => $queryResults)
        {
            $data[$tableName] = array();
            foreach ($queryResults as $index => $value)
            {
                $item = $marshaler->unmarshalItem($value);
                $data[$tableName][$item["Id"]] = $item;
            }            
        }
        return $data;        
    }
            
    function BatchPut($tableName,$key,$data)
    {
        if(!isset($key) || !isset($tableName) || !isset($data))
            return null;
        
        $marshaler = new Marshaler();
        
        if($this->batchWrite == null)
        {
            throw  new CommonException(500,"DBApiError","ErrorBatchCommand");
        }
        
        $data['Id'] = $key;
        
        if(array_key_exists ($tableName,$this->batchWrite['RequestItems']))
        {
            $this->batchWrite['RequestItems'][$tableName][] = array(
                'PutRequest' => [
                        'Item' =>$marshaler->marshalItem($data)
                ]
            );
        }
        else
        {
            $this->batchWrite['RequestItems'][$tableName] = array(
                [
                    'PutRequest' => [
                            'Item' =>$marshaler->marshalItem($data)
                    ]
                ]
            );
        }
        
        return $this;
    }
    
    function BatchDelete($tableName,$key)
    {        
        if(!isset($key) || !isset($tableName))
            return null;
        
        if($this->batchWrite == null)
        {
            throw  new CommonException(500,"DBApiError","ErrorBatchCommand");
        }
        
        if(array_key_exists ($tableName,$this->batchWrite['RequestItems']))
        {
            $this->batchWrite['RequestItems'][$tableName][] = array(
                'DeleteRequest' => [
                    'Key' => [
                        'Id' => ['S' => $key]
                    ]
                ]
            );
        }
        else
        {
            $this->batchWrite['RequestItems'][$tableName] = array(
                [
                    'DeleteRequest' => [
                        'Key' => [
                            'Id' => ['S' => $key]
                        ]
                    ]
                ]
            );
        }
        
        return $this;
    }
    
    function BatchWriteRun()
    {
        if($this->batchWrite == null)
        {
            throw  new CommonException(500,"DBApiError","ErrorBatchCommand");
        }
        if(count ($this->batchWrite['RequestItems']) == 0)
        {
            $this->batchWrite = null;
            return null;
        }       
        $response = DynamoDB::$dynamodb->batchWriteItem($this->batchWrite);
        $this->batchWrite = null;
        
        return $response;
    }
}