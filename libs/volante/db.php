<?php

Class Db2 {

    private $nombrebd; //
    private $link;
    private $stmt;
    private $error;
    static $_instance;


    /* La función construct es privada para evitar que el objeto pueda ser creado mediante new */
    private function __construct($namedb) {
        $this->setConexion($namedb);
        $this->conectar();
    }

    /* Método para establecer los parámetros de la conexión */

    private function setConexion($namedb = '') {
        $this->nombrebd = $namedb;
    }

    /* Evitamos el clonaje del objeto. Patrón Singleton */

    private function __clone() {

    }

    /* Función encargada de crear, si es necesario, el objeto.
      Esta es la función que debemos llamar desde fuera de la clase para instanciar el objeto, y así, poder utilizar sus métodos */

    public static function getInstance($namedb='') {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self($namedb);
        }
        return self::$_instance;
    }

    /* Realiza la conexión a la base de datos. */

    function exconectar() {
        //throw new Exception('Error al intentar conectar.');
        throw new Exception('');
    }

    private function conectar() {
        try {
            $this->link = odbc_connect('BDINPROP', "db2admin", "unisimon2015.*") or $this->exconectar();
            odbc_exec($this->link, "set schema db2admin");
        } catch (Exception $error) {
            print "<script>alert('No hay conexion con la base de datos');</script>";
            print $error->getMessage();
        }
    }

    //Obtener estado de la conexion
    public function getEstado() {
        if ($this->link) {
            return true;
        } else {
            return false;
        }
    }

    //Obtener Numero de filas
    public function getNumFilas() {
        if ($this->link) {
            $num = odbc_num_rows($this->stmt);
            return $num;
        } else {
            return 0;
        }
    }

    /* Método para ejecutar una sentencia sql */

    public function ejecutar($sql) {

        $this->stmt = odbc_exec($this->link, $sql);
        $this->error = odbc_error();
        return $this->stmt;
    }

    /* Método para establecer false en autocommit */

    public function setAutocommitFalse() {
        @odbc_autocommit($this->link, false);
    }

    /* Método para establecer false en autocommit */

    public function setAutocommitTrue() {
        @odbc_autocommit($this->link, true);
    }

    /* Método para establecer devolver la transaccion */

    public function setRollback() {
        @odbc_rollback($this->link);
    }

    /* Método para establecr coomit , guardar transaccion */

    public function commit() {
        @odbc_commit($this->link);
    }

    //**************metodos obtenedores*********

    public function getRta($rta)
    {

        if(odbc_num_rows($rta)>0){
            return odbc_fetch_array($rta);
        }
    }

    /* Método para obtener los resultados en un array php */
    public function getArrayDeResult($Res) {

        $arrayResut = array();

        $x = 1;

        while (odbc_fetch_row($Res)) {
            var_dump(odbc_num_fields($Res));
            exit;
            for ($y = 1; $y <= odbc_num_fields($Res); $y++) {
                $arrayResut[$x][$y] = odbc_result($Res, $y);
            }
            $x++;
        }

        return $arrayResut;
    }

    /* Método para ejecutar obtener los resultados (un registro a la vez) */

    public function getResult($Res) {
        $Campos = @odbc_fetch_array($Res);
        return $Campos;
    }
    public function getResultArray($result) {
        $rta = array();
        while($row=@odbc_fetch_array($result)){
            $rta[] = $row;

        }
        return $rta;
    }


    public function getFreeResult($Res) {
        odbc_free_result($Res);
    }

    /* Método para ejecutar obtener errores de la consulta */

    public function getError() {
        return $this->error;
    }

    public function getLink() {
        return $this->link;
    }

}

?>
