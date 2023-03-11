<?php
    class Calculatrice
    {
        // Properties
        protected $nb1;
        protected $nb2;

        function __construct($n1, $n2)
        {
            $this->nb1 = $n1;
            $this->nb2 = $n2;
        }

        // Methods
        function add()
        {
            return $this->nb1 + $this->nb2;
        }
        function substract()
        {
            return $this->nb1 - $this->nb2;
        }

        function multiply()
        {
            return $this->nb1 * $this->nb2;
        }

        function divide()
        {
            return $this->nb1 / $this->nb2;
        }
    }


    class CalculatriceAvancee extends Calculatrice
    {
        function modulo()
        {
            return $this->nb1 % $this->nb2;
        }

        function pow()
        {
            return pow($this->nb1,$this->nb2);
        }

        public static function brand() {
            echo "Texas Instrument";
        }
    }    


    // define variables and set to empty values
    $nb1 = $nb2 = 0;

    function test_input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $nb1 = test_input($_POST["nb1"]);
        $nb2 = test_input($_POST["nb2"]);

        $calc = new Calculatrice($nb1, $nb2);
        $calcAdvanced = new CalculatriceAvancee($nb1, $nb2);

        if (isset($_POST['+'])) {
            echo $calc->add();
        } else if (isset($_POST['-'])){
            echo $calc->substract();
        } else if (isset($_POST['/'])){
            echo $calc->divide();
        } else if (isset($_POST['*'])){
            echo $calc->multiply();
        } else if (isset($_POST['%'])){
            echo $calcAdvanced->modulo();
        } else if (isset($_POST['^'])){
            echo $calcAdvanced->pow();
        } else if (isset($_POST['brand'])){
            echo $calcAdvanced::brand();
        }

    }
?>