<?php

error_reporting(1);
ini_set('display_errors', '1');

include_once ("application" . DS . "Controller.php");

class basicoController extends Controller {

    public function __construct() {
        parent::__construct();
        $this->_usuario = $this->loadModel('usuario');
    }

    public function index() { }

    public function login($data){
      $array = array();
      $login = $this->_usuario->findByObject(
        array(
          'usuario' => $data->user, 
          'clave' => $this->hash($data->password)));
      $tipo = 'docente';
      if(!$login){
        $array['respuesta'] = false;
        $array['mensaje'] = "Erro Inicio de Sesi&oacute;n.";
        return json_encode($array);
      }
      $array['respuesta'] = true;
      $array['tipo'] = $login->getRol()->getDescripcion();
	    return  json_encode($array);
    }

    public function login_rfid($data){
      $array = array();
      $login = $this->_usuario->findByObject(
        array('rfid' => $data->rfid)
      );
      if(!$login){
        $array['respuesta'] = false;
        $array['mensaje'] = "Erro Inicio de Sesi&oacute;n.";
        return json_encode($array);
      }
      $array['respuesta'] = true;
      $array['tipo'] = $login->getRol()->getDescripcion();
      return  json_encode($array);
    }

    public function hash($clave){
      $hash = hash_init('sha1', HASH_HMAC, '4g6b6e832cf79');
      hash_update($hash, $clave);
      return hash_final($hash);
    }

}

?>
