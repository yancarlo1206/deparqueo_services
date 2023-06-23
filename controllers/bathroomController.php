<?php

error_reporting(1);
ini_set('display_errors', '1');

include_once ("application" . DS . "Controller.php");

class bathroomController extends Controller {

    public function __construct() {
        parent::__construct();
        $this->_tarjeta = $this->loadModel('tarjeta');
        $this->_tarifa = $this->loadModel('tarifa');
        $this->_tipoTarifa = $this->loadModel('tipotarifa');
        $this->_caja = $this->loadModel('caja');
        $this->_usuario = $this->loadModel('usuario');
	      $this->_variable = $this->loadModel('variable');
    }

    public function index() { }

    public function entrada_bathroom_rfid($data){
      $array = array();
      $fechaIngreso = new \DateTime();
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
      $fechaActual = new DateTime();
      if($tarjeta ->getFechaFin() < $fechaIngreso){
        $array['respuesta'] = false;
        $array['mensaje'] = 'Tarjeta Vencida - Vigencia hasta: '.$tarjeta->getFechaFin()->format('d/m/Y');
        return  json_encode($array);
      }
      $array['respuesta'] = true;
      $array['numero'] = 0;
      return  json_encode($array);
    }

}

?>
