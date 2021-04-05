<?php 

// depending on the number of records might need more memory.
ini_set('memory_limit', '2048M'); 

// including mysql databases connection settings
include 'connections.php'; 

// including a class to handle the asynchronous mysql connections.
include 'Classes/AsyncClient.php'; 

$dbs = [];
foreach ($connections as $con) {
    $dbs[] = new mysqli($con[0], $con[1], $con[2], $con[3], $con[4]); // init mysql connection.
}

// function to perform and output the results of the synchronous test 
$syncTest = function($dbs, $sql){
    $i = 0;
    $recordsTotal = 0;
    $timeTotal = 0;
    echo "Sync Test\n";
    foreach ($dbs as $db) {
        $starttime = microtime(true);
        $result = $db->query($sql);
        $res = $result->fetch_all();
        $endtime = microtime(true);    
        echo sprintf("DB $i Duration: %0.2f\n", ($endtime - $starttime));
        echo sprintf("DB $i Records: %d\n", count($res));
        $recordsTotal += count($res);
        $timeTotal += $endtime - $starttime;
        $i++;
    }
    echo sprintf("Total Duration: %0.2f\n", $timeTotal);
    echo sprintf("Total Records: %d\n", $recordsTotal);    
};

// function to perform and output the results of the asynchronous test 
$asyncTest = function($dbs, $sql){
    echo "Async Test\n";
    $async_client = new AsyncClient($dbs);
    $starttime = microtime(true);
    $res = $async_client->fetch($sql);
    $endtime = microtime(true);
    echo sprintf("Total Duration: %0.2f\n", $endtime - $starttime);
    echo sprintf("Total Records: %d\n", count($res));
};

// setting up sql statement to execute for both tests. 
$sql = "select * from bets where created_at between '2016-01-01' and '2016-10-31'";

// running synchronous test
$syncTest($dbs, $sql);

echo "\n";

// running asynchronous test
$asyncTest($dbs, $sql);
