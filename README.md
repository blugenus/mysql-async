# MySQL Async Text

1. Setup up a number of MySQL instances running on different machines 
2. Edit the connections.php file and set their hostname, username, password, database name and port. Please set the database name as `async`.
3. run `php prepare-dbs.php` to create 10 years worth of bets ranging from 100 to 1000 bets per day.
4. run `php async-test.php` to see the result.

## Result

Please view the image named "result.png". 

Running the select statement indicated in the test on 2 databases synchronously resulted in a total number of records of 336,046 records and took a total of 2.55 seconds

Running the query asynchronously resulted in the same number of records but took 1.88 seconds