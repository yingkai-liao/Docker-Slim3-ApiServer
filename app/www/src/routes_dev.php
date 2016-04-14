<?php
// Routes
//$app = new \Slim\App($settings);

$app->any("/test",function($request, $response, $args) 
{
    $db = new App\Core\DynamoDB();
    echo "deleteTable"."\n";
    $db->deleteTable('testTable1');
    $db->deleteTable('testTable2');
    echo "createTable"."\n";
    $db->createTable("testTable1");
    $db->createTable("testTable2");
    $data = array(
        "color"=>"red",
        "count"=>5,
        "common" => [
            array("id"=>1),
            array("id"=>2),
            array("id"=>4),
            array("id"=>5),
            ]
        );
    echo "putItem"."\n";
    $res = $db->putItem("testTable1", "testKey1", $data);
    $res = $db->putItem("testTable1", "testKey2", $data);
    $res = $db->putItem("testTable2", "testKey3", $data);
     echo json_encode($res)."\n";
    
    echo "getItem"."\n";
    $res = $db->getItem('testTable1', 'testKey1');
    echo json_encode($res)."\n";
    
    echo "BatchGetRun"."\n";
    $res = $db->BatchGetBegin()
            ->BatchGet('testTable1', 'testKey1')
            ->BatchGet('testTable1', 'testKey2')
            ->BatchGet('testTable2', 'testKey3')
            ->BatchGetRun();
    
    echo json_encode($res)."\n";
    
    echo "BatchWriteRun"."\n";
    $res = $db->BatchWriteBegin()
            ->BatchPut('testTable1', 'key1', $data)
            ->BatchPut('testTable1', 'key2', $data)
            ->BatchPut('testTable1', 'key3', $data)
            ->BatchWriteRun();
    echo json_encode($res)."\n";
    
    echo "BatchWriteRun"."\n";
    $res = $db->BatchWriteBegin()
            ->BatchDelete('testTable1', 'key1', $data)
            ->BatchDelete('testTable1', 'key2', $data)
            ->BatchDelete('testTable1', 'key3', $data)
            ->BatchDelete('testTable2', 'testKey3', $data)
            ->BatchDelete('testTable2', 'testKey2', $data)
            ->BatchWriteRun();
    echo json_encode($res)."\n";
    
    echo "done";
});

$app->any("/Init",function($request, $response, $args) 
{
    $response = $response->withHeader('Content-Type', 'text/text');
     $db = new App\Core\DynamoDB();
     $tableList = array(
         "playerToToken",
         "tokenToPlayer",
         "player"
     );
     foreach($tableList as $tableName)
     {
         echo "checkTable : $tableName\n";
         if(!$db->doesTableExist($tableName))
         {
             echo "createTable : $tableName\n";
             $db->createTable($tableName);
         }
     }
});