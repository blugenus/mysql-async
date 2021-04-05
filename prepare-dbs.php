<?php

// including database connections parameters. 
include 'connections.php';

foreach ($connections as $con) {
    // leaving database name empty as we will create it. 
    $db = new mysqli($con[0], $con[1], $con[2], '', $con[4]); 
    
    // create `async` database
    $db->query("CREATE DATABASE `async` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

    // create `bets` table
    $db->query("CREATE TABLE `async`.`bets` (
        `id` INT(11) NOT NULL AUTO_INCREMENT, 
        `created_at` TIMESTAMP, 
        `amount` DOUBLE(12,2) DEFAULT 0, 
        PRIMARY KEY (`id`) 
    ); ");

    // lets do some data from 1st January 2010 to 31st Decmeber 2020
    $date = new DateTime('2010-01-01 00:00:00'); 
    $endDate = new DateTime('2020-12-31 23:59:59'); 
    do {
        $bets = [];
        
        // between 100 and 1000 bets for day
        $betsOnDay = rand(100,1000); 

        // assign bet a random value in multiple of 0.10
        for ($i = 0; $i < $betsOnDay; $i++){
            $bet = rand(1,100) * 0.1;
            $bets[] = "('" . $date->format('Y-m-d H:i:s') . "'," . $bet . ")";
        }

        // using bulk insert to insert the day's bets in mysql.
        $db->query(
            "INSERT INTO `async`.`bets`(`created_at`,`amount`) VALUES " . implode(',', $bets)
        );

        // output progress on screen. 
        echo $date->format('Y-m-d') . ', Bets: ' . count($bets) . "\n";

        // adding 1 day to $date
        $date->modify('+1 day');
    } while ($date < $endDate);
}



