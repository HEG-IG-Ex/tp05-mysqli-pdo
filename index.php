<?php
    require_once("./dao.php");
    require_once("./table.php");

    define("SRCH_BY_LOC_QUERY", "SELECT * FROM npa WHERE npa_localite LIKE ?");
    define("SRCH_BY_NPA_QUERY", "SELECT * FROM npa WHERE npa_code LIKE ?");

    
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
            if (is_numeric($search)) {
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
                    $rows = fetch_records_mysqli($query, $search);
                    generate_table($rows);
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
                    $rows = fetch_records_pdo($query, $search);
                    generate_table($rows);
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