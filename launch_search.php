<?php
// TODO: Transfer to env files
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PSW", "");
define("DB_NAME", "npa");

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    if (isset($_POST["search"])) {
        $search = test_input($_POST["search"]) . "%";
    } else {
        throw new ValueError("Search field not set");
    }

    try {

        /************************* CONNECTION *******************************/

        /* activate reporting */
        $driver = new mysqli_driver();
        $driver->report_mode = MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;

        /* if the connection fails, a mysqli_sql_exception will be thrown */
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PSW, DB_NAME);

        //No Exceptions were thrown, we connected successfully, yay!
        echo "Success, we connected without failure! <br />";
        echo "Connection Info: " . $mysqli->host_info  . PHP_EOL . "\n\r<br>";


        /************************* QUERY *******************************/

        /* create a prepared statement */
        $stmt = $mysqli->prepare("SELECT * FROM npa WHERE npa_localite LIKE ?");

        /* bind parameters for markers */
        $stmt->bind_param("s", $search);

        /* execute statement */
        $stmt->execute();

        /* Get result */
        $result = $stmt->get_result();

        /* Control if result not empty */
        if($result->num_rows == 0){throw new ValueError("No District Found");}

        /* fetch associative array & display */
        while ($row = $result->fetch_assoc()) {
            printf("%s (%s) - %s\n\r<br>", $row["npa_id"], $row["npa_code"], $row["npa_localite"]);
        }
        
    } catch (mysqli_sql_exception $e) {
        echo "SQL Exceptions";
        error_log($e->__toString());
    } catch (ValueError $e) {
        echo "Value Exceptions";
        error_log($e->__toString());
    } catch (Exception $e) {
        echo "General Exceptions";
        error_log($e->__toString());
    } finally {
        $mysqli->close();
        exit();
    }

}
