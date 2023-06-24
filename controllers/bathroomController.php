<?php

error_reporting(1);
ini_set('display_errors', '1');

include_once ("application" . DS . "Controller.php");

class bathroomController extends Controller {

    public function __construct() {
        parent::__construct();
        $this->_tarjeta = $this->loadModel('tarjetabathroom');
        $this->_ingreso = $this->loadModel('ingresobathroom');
        $this->_tarifa = $this->loadModel('tarifa');
        $this->_tipoTarifa = $this->loadModel('tipotarifa');
        $this->_usuario = $this->loadModel('usuario');
	      $this->_variable = $this->loadModel('variable');
    }

    public function index() { }

    public function entrada_bathroom_rfid($data){
      $array = array();
      $tarjeta = $this->_tarjeta->findByObject(
        array('rfid' => $data->rfid)
      );
      if(!$tarjeta){
        $array['respuesta'] = false;
        $array['mensaje'] = 'Tarjeta no Existe';
        return  json_encode($array);
      }
      if($tarjeta && $tarjeta->getEstado() != 1){
        $array['respuesta'] = false;
        $array['mensaje'] = 'Tarjeta Inactiva';
        return  json_encode($array);
      }
      if($tarjeta && $tarjeta->getEntradas() == 0){
        $array['respuesta'] = false;
        $array['mensaje'] = 'Tarjeta sin Entradas';
        return  json_encode($array);
      }
      $ingreso = $this->ingreso_bathroom($tarjeta);
      if($ingreso){
        $this->_tarjeta->getInstance()->setEntradas($tarjeta->getEntradas()-1);
        $this->_tarjeta->update();
        $array['respuesta'] = true;
        $array['mensaje'] = "Entradas restantes: ".$this->_tarjeta->getInstance()->getEntradas();
      }else{
        $array['respuesta'] = false;
        $array['mensaje'] = 'Error en el Proceso';
      }
      return  json_encode($array);
    }

    private function ingreso_bathroom($tarjeta=null){
      $fechaIngreso = new \DateTime();
      $this->_ingreso->getInstance()->setFecha($fechaIngreso);
      $this->_ingreso->getInstance()->setFechaIngreso($fechaIngreso);
      $this->_ingreso->getInstance()->setTarjeta($tarjeta);
      try {
        $this->_ingreso->save();
        return true;
      } catch (Exception $e) {
        echo $e;
        return false;        
      }
    }

}

?>
