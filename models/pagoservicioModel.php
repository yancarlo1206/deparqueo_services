<?php

include_once ("application" . DS . "Model.php");

class pagoservicioModel extends Model {
    public function __construct() {
        parent::__construct(); 
        $this->instance = $this->loadObjeto('Pagoservicio'); 
    }
}
?>