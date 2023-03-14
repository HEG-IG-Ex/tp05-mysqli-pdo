<?php
// TODO: Transfer constant to env files
// TODO: separate code into a functions.php and make a simple call for each methods instead of inserting all the code here
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PSW", "");
define("DB_NAME", "npa");
define("DB_DSN", "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME);
define("LOG_FILE", "C:/xampp/htdocs/WebProjects/TP05/errors.log");
define("SRCH_BY_LOC_QUERY", "SELECT * FROM npa WHERE npa_localite LIKE ?");
define("SRCH_BY_NPA_QUERY", "SELECT * FROM npa WHERE npa_code LIKE ?");


class TableRows extends RecursiveIteratorIterator
{
    function __construct($it)
    {
        parent::__construct($it, self::LEAVES_ONLY);
    }

    public function current()
    {
        return "<td>" . parent::current() . "</td>";
    }

    public function beginChildren()
    {
        echo "<tr>";
    }

    public function endChildren()
    {
        echo "</tr>" . "\n";
    }
}

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    if (isset($_POST["search"])) {
        $search = test_input($_POST["search"]);
        if(is_numeric($search)){
            $query = SRCH_BY_NPA_QUERY;
        } else {
            $query = SRCH_BY_LOC_QUERY;
        }
        $search .= "%";

    } else {
        throw new ValueError("Search field not set");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Hello, world!</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta name="description" content="" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="icon" href="favicon.png">
    <style>
        table,
        td,
        th {
            border: 1px solid;
        }

        table {
            width: 50%;
            border-collapse: collapse;
        }
    </style>
</head>

<body>
    <div class="container">
        <header class="row mb-4 col-lg-8 mx-auto">
            <h1>TP05</h1>
        </header>

        <form class="row g-3 col-lg-8 mx-auto" action="" method="POST">

            <div class="row g-3">
                <div class="col-md-3">
                    <input type="input" class="form-control" id="search" name="search" placeholder="Type an NPA">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary" type="submit" id="go" name="go">Go</button>
                </div>
            </div>

            <h2>Exercice 1 - Search MYSQLI</h2>

            <div class="row g-3">
                <?php
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {

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

                        echo "<table>" . PHP_EOL;
                        echo "<tr><th>Id</th><th>Npa</th><th>Locality</th></tr>" . PHP_EOL;

                        /* fetch associative array & display */
                        foreach (new TableRows(new RecursiveArrayIterator($result->fetch_all(MYSQLI_ASSOC))) as $k => $v) {
                            echo $v;
                        }

                        echo "</table>";
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
                ?>
            </div>



            <div class="row g-3">
                <hr>
                <h2>Exercice 2 - Search PDO</h2>
            </div>

            <div class="row g-3">
                <?php
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

                        echo "<table>" . PHP_EOL;
                        echo "<tr><th>Id</th><th>Npa</th><th>Locality</th></tr>" . PHP_EOL;

                        // set the resulting array to associative
                        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
                        foreach (new TableRows(new RecursiveArrayIterator($stmt->fetchAll())) as $k => $v) {
                            echo $v;
                        }

                        echo "</table>" . PHP_EOL;
                    } catch (PDOException $e) {
                        echo "SQL Exceptions";
                        error_log($e->__toString(), 3, LOG_FILE);
                    } finally {
                        $pdo = null;
                    }
                }
                ?>
            </div>


            <div class="row g-3">
                <hr>
                <h2>Exercice 3 - Search Google</h2>
            </div>

            <div class="row g-3">

            </div>


        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

</html>