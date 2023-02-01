<?php

include_once ("application" . DS . "Model.php");

class nopagoservicioModel extends Model {
    public function __construct() {
        parent::__construct(); 
        $this->instance = $this->loadObjeto('Nopagoservicio'); 
    }
}
?>