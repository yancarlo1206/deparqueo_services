<?php

include_once ("application" . DS . "Model.php");

class ingresonormalModel extends Model {
    public function __construct() {
        parent::__construct(); 
        $this->instance = $this->loadObjeto('Ingresonormal'); 
    }
}
?>