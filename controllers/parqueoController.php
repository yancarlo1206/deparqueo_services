<?php

include_once ("application" . DS . "Controller.php");

class parqueoController extends Controller {

    public function __construct() {
        parent::__construct();
        $this->_ingreso = $this->loadModel('ingreso');
        $this->_ingresoNormal = $this->loadModel('ingresonormal');
        $this->_ingresoTarjeta = $this->loadModel('ingresotarjeta');
        $this->_pago = $this->loadModel('pago');
        $this->_pagoServicio = $this->loadModel('pagoservicio');
        $this->_tarjeta = $this->loadModel('tarjeta');
        $this->_tipoVehiculo = $this->loadModel('tipovehiculo');
        $this->_tarifa = $this->loadModel('tarifa');
        $this->_tipoTarifa = $this->loadModel('tipotarifa');
        $this->_caja = $this->loadModel('caja');
        $this->_usuario = $this->loadModel('usuario');
	      $this->_variable = $this->loadModel('variable');
    }

    public function index() { }

    public function entrada($data){
      $tipoVehiculo = $data->tipovehiculo;
      $casco = 0;
      if($tipoVehiculo == 2){
        $casco = $data->casco;
      }
      $array = array();
      $tipoVehiculo = $this->_tipoVehiculo->get($tipoVehiculo);
      $fechaIngreso = new \DateTime();
      $temp = $this->_ingreso->dql("SELECT i FROM Entities\Ingreso i WHERE i.fecha =:fecha",
          array('fecha' => $fechaIngreso->format('Y-m-d')));
      $cuenta = str_pad(count($temp)+1, 3, "0", STR_PAD_LEFT);
      $numero = $tipoVehiculo->getResumen().$fechaIngreso->format('dmy').$cuenta;
      $this->_ingreso = $this->loadModel('ingreso');
      $this->_ingreso->getInstance()->setTipo($this->_tipoVehiculo->getInstance());
      $this->_ingreso->getInstance()->setFecha($fechaIngreso);
      $this->_ingreso->getInstance()->setFechaingreso($fechaIngreso);
      $this->_ingreso->getInstance()->setNumero($numero);
      $this->_ingreso->getInstance()->setCasco($casco);
      try {
        $this->_ingreso->save();
        $this->_ingresoNormal->getInstance()->setId($this->_ingreso->getInstance());
        $this->_ingresoNormal->save();
        $array['respuesta'] = true;
        $array['numero'] = $this->_ingreso->getInstance()->getNumero();
        $array['tipo'] = $tipoVehiculo->getId();  
        $array['descripcion'] = $tipoVehiculo->getDescripcion();
        $array['fecha'] = $this->_ingreso->getInstance()->getFechaingreso()->format('d-m-Y H:i');
        if($this->_ingreso->getInstance()->getCasco() > 0){
          $array['casco'] = $this->_ingreso->getInstance()->getCasco();
        }
      } catch (Exception $e) {
        $array['respuesta'] = false;
        $array['mensaje'] = 'Error en el Proceso';
      }
      return  json_encode($array);
    }

    public function entrada_rfid($data){
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
      $temp = $this->_ingresoTarjeta->dql("SELECT it FROM Entities\IngresoTarjeta it JOIN it.id i 
        WHERE it.tarjeta =:tarjeta AND i.fechasalida IS NULL",
          array('tarjeta' => $tarjeta->getRfid()));
      if($temp){
        $array['respuesta'] = false;
        $array['mensaje'] = 'Tarjeta se Encuentra registrada';
        return  json_encode($array);
      }
      $tempMultiple = $this->_ingresoTarjeta->dql("SELECT it FROM Entities\IngresoTarjeta it JOIN it.id i
        JOIN it.tarjeta tar 
        WHERE i.fechasalida IS NULL AND tar.cliente =:cliente",
          array('cliente' => $tarjeta->getCliente()->getId()));
      if($tempMultiple){
        $array['respuesta'] = false;
        $array['mensaje'] = 'El Usuario ya se Encuentra Ingresado';
        return  json_encode($array);
      }
      $this->_ingreso->getInstance()->setTipo(
        $this->_tipoVehiculo->get($tarjeta->getTipoVehiculo()->getId())
      );
      $this->_ingreso->getInstance()->setFecha($fechaIngreso);
      $this->_ingreso->getInstance()->setFechaingreso($fechaIngreso);
      try {
        $this->_ingreso->save();
        $this->_ingresoTarjeta->getInstance()->setId($this->_ingreso->getInstance());
        $this->_ingresoTarjeta->getInstance()->setTarjeta($tarjeta);
        $this->_ingresoTarjeta->save();
        $array['respuesta'] = true;
        $array['numero'] = 0;
        $array['tipo'] = $tarjeta->getTipoVehiculo()->getId();
        $array['descripcion'] = $tarjeta->getTipoVehiculo()->getDescripcion();
        $array['fecha'] = $this->_ingreso->getInstance()->getFechaingreso()->format('d-m-Y H:i');
      } catch (Exception $e) {
        $array['respuesta'] = false;
        $array['mensaje'] = 'Error en el Proceso';
      }
      return  json_encode($array);
    }

    public function salida($data){
      $cant = strlen($data->ticket);
      if($cant == 12){
        return $this->salida_ticket($data);
      }else{
        return $this->salida_rfid($data);
      }
    }

    public function salida_ticket($data){
      $array = array();
      $ticket = $data->ticket;
      $this->_ingreso->findByObject(array('numero' => $ticket));
      if(!$this->_ingreso->getInstance()->getNumero()){
        $array['respuesta'] = false;
        $array['mensaje'] = 'No se encuentra el TICKET';
        return  json_encode($array);
      }
      if($this->_ingreso->getInstance()->getFechaSalida()){
        $array['respuesta'] = false;
        $array['mensaje'] = 'El TICKET ya Salio';
        return  json_encode($array);
      }
      $this->_ingresoNormal->get($this->_ingreso->getInstance()->getId());
      $salida = false;
      if(count($this->_ingresoNormal->getInstance()->getNoPagos())){
          $salida = true;
      }
      if(count($this->_ingresoNormal->getInstance()->getCancelados())){
          //$salida = true;
	$array['respuesta'] = false;
        $array['mensaje'] = 'El TICKET esta Anulado';
        return  json_encode($array);
      }
      if(!$salida && !count($this->_ingresoNormal->getInstance()->getPagos())){
        $array['respuesta'] = false;
        $array['mensaje'] = 'El TICKET no tiene un PAGO registrado';
        return  json_encode($array);
      }
      $this->_ingreso->getInstance()->setFechaSalida(new \DateTime());
      try {
        $this->_ingreso->save();
        $array['respuesta'] = true;
        $array['ticket'] = $this->_ingreso->getInstance()->getNumero();
        $array['pago'] = true;
        $array['fecha'] = $this->_ingreso->getInstance()->getFechaSalida()->format('d-m-Y H:i');
      } catch (Exception $e) {
        $array['respuesta'] = false;
        $array['mensaje'] = 'Error en el Proceso';
      }
      return  json_encode($array);
    }

    public function salida_rfid($data){
      $array = array();
      $tarjeta = $this->_tarjeta->findByObject(
        array('rfid' => $data->ticket)
      );
      if(!$tarjeta){
        $array['respuesta'] = false;
        $array['mensaje'] = 'RFID no existe';
        return  json_encode($array);
      }
      $ingresoTarjeta = $this->_ingresoTarjeta->dql("SELECT it FROM Entities\IngresoTarjeta it 
        JOIN it.id i WHERE it.tarjeta =:tarjeta AND i.fechasalida IS NULL",
          array('tarjeta' => $tarjeta->getRfid()));
      if(!$ingresoTarjeta){
        $array['respuesta'] = false;
        $array['mensaje'] = 'No se encuentra el Ingreso de la Tarjeta';
        return  json_encode($array);
      }
      $this->_ingreso->get($ingresoTarjeta[0]->getId()->getId());
      $this->_ingreso->getInstance()->setFechaSalida(new \DateTime());
      try {
        $this->_ingreso->save();
        $array['respuesta'] = true;
        $array['tipo'] = $tarjeta->getTipoVehiculo()->getId();
        $array['descripcion'] = $tarjeta->getTipoVehiculo()->getDescripcion();
        $array['fecha'] = $this->_ingreso->getInstance()->getFechaSalida()->format('d-m-Y H:i');
      } catch (Exception $e) {
        $array['respuesta'] = false;
        $array['mensaje'] = 'Error en el Proceso';
      }
      return  json_encode($array);
    }

}

?>