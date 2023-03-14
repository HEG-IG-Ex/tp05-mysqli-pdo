<?php
// TODO: Transfer to env files
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_DSN", "mysql:host=".DB_HOST.";dbname=".DB_NAME);
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

        /************************* MYSQLI CONNECTION *******************************/

        /* activate reporting */
        $driver = new mysqli_driver();
        $driver->report_mode = MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;

        /* if the connection fails, a mysqli_sql_exception will be thrown */
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PSW, DB_NAME);

        //No Exceptions were thrown, we connected successfully, yay!
        echo "Success, we connected without failure! <br />";
        echo "Connection Info: " . $mysqli->host_info  . PHP_EOL . "\n\r<br>";


        /************************* MYSQLI QUERY *******************************/

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

        /* fetch associative array & displayg */
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

    try{
         /************************* PDO CONNECTION *******************************/

         $pdo = new PDO(DB_DSN, DB_USER, DB_PSW);
         // set the PDO error mode to exception
         $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
         //No Exceptions were thrown, we connected successfully, yay!
         echo "Success, we connected without failure! <br />";
         echo "Connection Info: " . $pdo->getAttribute(constant("PDO::ATTR_SERVER_INFO"))  . PHP_EOL . "\n\r<br>";
 
 
         /************************* PDO QUERY *******************************/

        // prepare sql and bind parameters
        $stmt = $pdo->prepare("SELECT * FROM npa WHERE npa_localite LIKE :search_string");
        $stmt->bindParam(':search_string', $search);
        $stmt->execute();

    } catch (PDOException $e) {
        echo "SQL Exceptions";
        error_log($e->__toString());
    } finally {
        $pdo=null;
    }

}
