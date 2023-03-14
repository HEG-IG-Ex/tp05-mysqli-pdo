<?php
// TODO: Transfer constant to env files
// TODO: separate code into a functions.php and make a simple call for each methods instead of inserting all the code here
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PSW", "");
define("DB_NAME", "npa");
define("DB_DSN", "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME);
define("LOG_FILE", "C:/xampp/htdocs/WebProjects/TP05/errors.log");


function fetch_records_mysqli($query, $search)
{
    try {

        /************************* MYSQLI CONNECTION *******************************/

        /* activate reporting */
        $driver = new mysqli_driver();
        $driver->report_mode = MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;

        /* if the connection fails, a mysqli_sql_exception will be thrown */
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PSW, DB_NAME);

        //No Exceptions were thrown, we connected successfully, yay!
        echo "Success, we connected without failure!" . PHP_EOL . "</br>";
        echo "Connection Info: " . $mysqli->host_info  . PHP_EOL . "</br>";


        /************************* MYSQLI QUERY *******************************/

        /* create a prepared statement */
        $stmt = $mysqli->prepare($query);

        /* bind parameters for markers */
        $stmt->bind_param("s", $search);

        /* execute statement */
        $stmt->execute();

        /* Get result */
        $result = $stmt->get_result();

        /* Control if result not empty */
        if ($result->num_rows == 0) {
            throw new ValueError("No District Found");
        }

        return $result->fetch_all(MYSQLI_ASSOC);

    } catch (mysqli_sql_exception $e) {
        echo "SQL Exceptions";
        error_log($e->__toString(), 3, LOG_FILE);
    } catch (ValueError $e) {
        echo "Value Exceptions";
        error_log($e->__toString(), 3, LOG_FILE);
    } catch (Exception $e) {
        echo "General Exceptions";
        error_log($e->__toString(), 3, LOG_FILE);
    } finally {
        $mysqli->close();
    }
}

function fetch_records_pdo($query, $search)
{
    try {
        /************************* PDO CONNECTION *******************************/

        $pdo = new PDO(DB_DSN, DB_USER, DB_PSW);
        // set the PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //No Exceptions were thrown, we connected successfully, yay!
        echo "Success, we connected without failure! " . PHP_EOL . "</br>";
        echo "Connection Info: " . $pdo->getAttribute(constant("PDO::ATTR_SERVER_INFO"))  . PHP_EOL . "</br>";


        /************************* PDO QUERY *******************************/

        // prepare sql and bind parameters
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(1, $search, PDO::PARAM_STR);
        $stmt->execute();

        // Add a new column to each row
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        echo "SQL Exceptions";
        error_log($e->__toString(), 3, LOG_FILE);
    } finally {
        $pdo = null;
    }
}
