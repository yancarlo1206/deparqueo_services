<?php

error_reporting(1);
ini_set('display_errors', '1');

include_once ("application" . DS . "Controller.php");

class parqueoController extends Controller {

    public function __construct() {
        parent::__construct();
        $this->_ingreso = $this->loadModel('ingreso');
        $this->_ingresoNormal = $this->loadModel('ingresonormal');
        $this->_ingresoTarjeta = $this->loadModel('ingresotarjeta');
        $this->_pago = $this->loadModel('pago');
        $this->_pagoServicio = $this->loadModel('pagoservicio');
        $this->_pagoSancion = $this->loadModel('pagosancion');
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
      $temp = $this->_ingresoTarjeta->dql("SELECT it FROM Entities\IngresoTarjeta it JOIN it.id i 
        WHERE it.tarjeta =:tarjeta AND i.fechasalida IS NULL",
          array('tarjeta' => $tarjeta->getRfid()));
      if($temp){
        $array['respuesta'] = false;
        $array['mensaje'] = 'Tarjeta ya tiene una Entrada Registrada';
        return  json_encode($array);
      }
      /*$tempMultiple = $this->_ingresoTarjeta->dql("SELECT it FROM Entities\IngresoTarjeta it JOIN it.id i
        JOIN it.tarjeta tar 
        WHERE i.fechasalida IS NULL AND tar.cliente =:cliente",
          array('cliente' => $tarjeta->getCliente()->getId()));
      if($tempMultiple){
        $array['respuesta'] = false;
        $array['mensaje'] = 'El Usuario ya tiene una de sus Tarjetas con Entrada';
        return  json_encode($array);
      }*/
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

        $datetime1 = new DateTime($fechaIngreso->format('Y-m-d'));
        $datetime2 = new DateTime($tarjeta->getFechaFin()->format('Y-m-d'));
        $interval = $datetime1->diff($datetime2);
        //$dias = $interval->format('%R%a días');
        $dias = $interval->format('%R%a');
        $dias = $dias - 1;
        $dias = $dias." días";
        $dias = str_replace("+","",$dias);

        $array['respuesta'] = true;
        $array['numero'] = 0;
        $array['tipo'] = $tarjeta->getTipoVehiculo()->getId();
        $array['descripcion'] = $tarjeta->getTipoVehiculo()->getDescripcion();
        $array['fecha'] = $this->_ingreso->getInstance()->getFechaingreso()->format('d-m-Y H:i');
        $array['vigencia'] = 'Vencimiento: '.$dias.'.';
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
      //Validar que los pagos que tenga esten dentro del tiempo establecido//
      $pagos = $this->_pago->findBy(array('ingreso' => $this->_ingreso->getInstance()->getId()));
      $fechaEntrada = $this->_ingreso->getInstance()->getFechaIngreso();
      $fechaPago = $pagos[0]->getFecha();
      $this->_variable->get(2);
      $vencido = $this->_variable->getInstance()->getValor();
      $minutosLimite = $vencido;
      $fechaLimiteNoVencida = $fechaPago->modify('+'.$minutosLimite.' minute');
      $fechaLimite = $fechaEntrada->modify('+'.$minutosLimite+$vencido.' minute');
      $fechaLimiteComparar = $fechaLimite;
      $fechaActualComparar = new \DateTime();
      if($fechaLimiteNoVencida < $fechaActualComparar){
          $fechaEntrada = $fechaLimiteNoVencida;
          $fechaSalida = new \DateTime();
          $fechaIntervalo = $fechaEntrada->diff($fechaSalida);
          $array['respuesta'] = false;
          $array['mensaje'] = 'El TICKET excedio el tiempo de vencimiento';
          return  json_encode($array);
        }

      ///////////////////////////////////////////////////////////////////////
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
      $tipoCliente = $tarjeta->getCliente()->getTipoCliente()->getId();
      $tipoTarifa = $tarjeta->getTarifa()->getTipoTarifa()->getId();
      $this->_ingreso->get($ingresoTarjeta[0]->getId()->getId());
      ///VALIDAR SI LA SALIDA ES AL SIGUIENTE DIA///
      $fechaEntrada = $this->_ingreso->getInstance()->getFecha();
      $fechaSalida = new \DateTime();
      $fechaIntervalo = $fechaEntrada->diff($fechaSalida);
      $dias = $fechaIntervalo->format('%d');
      if($dias > 0 && $tipoCliente != 5 && $tipoTarifa != 7){
        $fecha =  new \DateTime();
        $fechaIni = $fecha->format('Y-m-d')." 00:00:00";
        $fechaFin = $fecha->format('Y-m-d')." 23:59:59";
        $pagoSancion = $this->_pagoSancion->dql(
          "SELECT p FROM Entities\PagoSancion p WHERE p.documento =:tarjeta AND p.fecha >=:fechaIni AND p.fecha <=:fechaFin
          AND (p.tiposancion = 5 OR p.tiposancion = 6)",
          array('tarjeta' => $ingresoTarjeta[0]->getTarjeta(), 'fechaIni' => $fechaIni, 'fechaFin' => $fechaFin));
        if(!count($pagoSancion)){
          $array['respuesta'] = false;
          $array['mensaje'] = 'La Tarjeta debe Pagar una Sancion';
          return  json_encode($array);
        }
      }
      //////////////////////////////////////////////
      $this->_ingreso->getInstance()->setFechaSalida(new \DateTime());
      try {
        $this->_ingreso->save();

        $datetime1 = new DateTime($fechaEntrada->format('Y-m-d'));
        $datetime2 = new DateTime($tarjeta->getFechaFin()->format('Y-m-d'));
        $interval = $datetime1->diff($datetime2);
        //$dias = $interval->format('%R%a días');
        $dias = $interval->format('%R%a');
        $dias = $dias - 1;
        $dias = $dias." días";
        $dias = str_replace("+","",$dias);

        $array['respuesta'] = true;
        $array['tipo'] = $tarjeta->getTipoVehiculo()->getId();
        $array['descripcion'] = $tarjeta->getTipoVehiculo()->getDescripcion();
        $array['fecha'] = $this->_ingreso->getInstance()->getFechaSalida()->format('d-m-Y H:i');
        $array['vigencia'] = 'Vencimiento: '.$dias.'.';
      } catch (Exception $e) {
        $array['respuesta'] = false;
        $array['mensaje'] = 'Error en el Proceso';
      }
      return  json_encode($array);
    }

}

?>
