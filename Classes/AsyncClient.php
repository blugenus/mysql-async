<?php

class AsyncClient
{
    /**
     * All mysqli connections. 
     *
     * @var array - mysqli conntections
     */
    private $dbs = [];

    /**
     * constructs the class
     *
     * @param $dbs - array of mysqli connections. 
     * @return void
     */
    public function __construct(&$dbs)
    {
        $this->dbs = $dbs;
    }

    /**
     * Submits the SQL statement to all connections and waits for them to finish. 
     *
     * @param $sql - SQL string to execute.
     * @return array - Containing the results from all databases. 
     */
    public function fetch($sql)
    {
        foreach ($this->dbs as $db) {
            $db->query($sql, MYSQLI_ASYNC);
        }
        return $this->poll();
    }

    /**
     * Submits the SQL statement to all connections and waits for them to finish. 
     *
     * based upon https://www.php.net/manual/en/mysqli.poll.php
     * 
     * @return array - 
     */
    private function poll()
    {
        $outcome = [];

        $processed = 0;
        do {
            // setting reads, erros and reject = array of mysqli connections
            $reads = $errors = $reject = $this->dbs;

            // will check if any of the connection has completed. 
            if (mysqli_poll($reads, $errors, $reject, 10)) {

                // processing those connections that have been executed
                foreach ($reads as $read) {
                    if ($result = $read->reap_async_query()) {
                        $outcome = array_merge($outcome, $result->fetch_all());
                        if (is_object($result)) {
                            mysqli_free_result($result);
                        }
                    } else {
                        die(sprintf("MySQLi Error: %s", mysqli_error($read)));
                    }
                    $processed++;
                }
            }
        } while ($processed < count($this->dbs));
        return $outcome;
    }

}
