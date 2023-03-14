<?php

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

function generate_table($rows){
    foreach ($rows as &$row) {
        $row["links"] = "<a href='https://www.google.ch/?q=" . $row['npa_localite'] . "'>https://www.google.ch/?q=" . $row['npa_localite'] . "</a>";
    }

    echo "<table>" . PHP_EOL;
    echo "<tr><th>Id</th><th>Npa</th><th>Locality</th><th>Search Links</th></tr>" . PHP_EOL;

    /* fetch associative array & display */
    foreach (new TableRows(new RecursiveArrayIterator($rows)) as $k => $v) {
        echo $v;
    }

    echo "</table>";
}

       

 