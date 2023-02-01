<?php

//error_reporting(E_ALL);
error_reporting(0);
ini_set('display_errors', '1');
//define('DS', DIRECTORY_SEPARATOR);
include_once ("application" . DS . "Controller.php");

class basicoController extends Controller {

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
    }

    public function index() { }

    public function entrada($data){
      $array = array();
      $tipoVehiculo = $this->_tipoVehiculo->get($data->tipovehiculo);
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
      try {
        $this->_ingreso->save();
        $this->_ingresoNormal->getInstance()->setId($this->_ingreso->getInstance());
        $this->_ingresoNormal->save();
        $array['respuesta'] = true;
        $array['numero'] = $this->_ingreso->getInstance()->getNumero();
        $array['tipo'] = $tipoVehiculo->getId();  
        $array['descripcion'] = $tipoVehiculo->getDescripcion();
        $array['fecha'] = $this->_ingreso->getInstance()->getFechaingreso()->format('d-m-Y h:i');
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
        $array['mensaje'] = 'RFID no existe';
        return  json_encode($array);
      }
      $temp = $this->_ingresoTarjeta->dql("SELECT it FROM Entities\IngresoTarjeta it JOIN it.id i 
        WHERE it.tarjeta =:tarjeta AND i.fechasalida IS NULL",
          array('tarjeta' => $tarjeta->getRfid()));
      if($temp){
        $array['respuesta'] = false;
        $array['mensaje'] = 'RFID ya se registro';
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
        $array['fecha'] = $this->_ingreso->getInstance()->getFechaingreso()->format('d-m-Y h:i');
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
        $array['mensaje'] = 'El TICKET ya se encuentra registrado';
        return  json_encode($array);
      }
      $this->_ingresoNormal->get($this->_ingreso->getInstance()->getId());
      $salida = false;
      if(count($this->_ingresoNormal->getInstance()->getNoPagos())){
          $salida = true;
      }
      if(count($this->_ingresoNormal->getInstance()->getCancelados())){
          $salida = true;
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
        $array['fecha'] = $this->_ingreso->getInstance()->getFechaSalida()->format('d-m-Y h:i');
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
        $array['fecha'] = $this->_ingreso->getInstance()->getFechaSalida()->format('d-m-Y h:i');
      } catch (Exception $e) {
        $array['respuesta'] = false;
        $array['mensaje'] = 'Error en el Proceso';
      }
      return  json_encode($array);
    }

    public function ticket($data){
      $array = array();
      $ticket = $data->ticket;
      $this->_ingreso->findByObject(array('numero' => $ticket));
      $this->_pago->findByObject(array('ingreso' => $this->_ingreso->getInstance()->getId()));
      if(!$this->_ingreso->getInstance()->getNumero()){
        $array['respuesta'] = false;
        $array['mensaje'] = 'No se encuentra el TICKET';
        return  json_encode($array);
      }
      $array['respuesta'] = true;
      $array['ticket'] = $this->_ingreso->getInstance()->getNumero();
      $array['fecha'] = $this->_ingreso->getInstance()->getFecha()->format('d-m-Y');
      $array['tipoVehiculo'] = $this->_ingreso->getInstance()->getTipo()->getDescripcion();
      $array['fechaEntrada'] = $this->_ingreso->getInstance()->getFechaIngreso()->format('d-m-Y h:i');
      if($this->_ingreso->getInstance()->getFechaSalida()){
        $array['fechaSalida'] = $this->_ingreso->getInstance()->getFechaSalida()->format('d-m-Y h:i');
      }
      if($this->_pago->getInstance()->getId()){
        $array['facturaVenta'] = rand(1,1000);
        $array['valorPagado'] = $this->_pago->getInstance()->getValor()+$this->_pago->getInstance()->getIva();
        $array['valor'] = $this->_pago->getInstance()->getValor();
        $array['iva'] = $this->_pago->getInstance()->getIva();
        $array['entrego'] = $this->_pago->getInstance()->getEntrego();
        $array['cambio'] = $this->_pago->getInstance()->getCambio();
      }else{
        $fechaEntrada = $this->_ingreso->getInstance()->getFechaIngreso();
        $fechaSalida = new \DateTime();
        $fechaIntervalo = $fechaEntrada->diff($fechaSalida);
        $array['valorPorPagar'] = $this->ValorPorPagar($this->_ingreso->getInstance(),$fechaIntervalo);
      }

      return  json_encode($array);
    }

    public function valorPorPagar($ingreso=null, $fechaIntervalo=null){
        $tarifaHora = $this->_tarifa->dql("SELECT t FROM Entities\Tarifa t 
            WHERE t.fechainicio <=:fecha AND t.fechafin >=:fecha 
            AND t.tipovehiculo =:tipoVehiculo AND t.tipotarifa = 1",
            array('fecha' => new \DateTime(), 'tipoVehiculo' => $ingreso->getTipo()->getId()));
        $tarifaFraccion = $this->_tarifa->dql("SELECT t FROM Entities\Tarifa t 
            WHERE t.fechainicio <=:fecha AND t.fechafin >=:fecha 
            AND t.tipovehiculo =:tipoVehiculo AND t.tipotarifa = 2",
            array('fecha' => new \DateTime(), 'tipoVehiculo' => $ingreso->getTipo()->getId()));
        $intervaloHora = $fechaIntervalo->format("%h");
        $intervaloMinuto = $fechaIntervalo->format("%i");
        $valorTemporalHora = 0;
        $valorTemporalFraccion = 0;
        if($intervaloHora > 0){
            $valorTemporalHora = $intervaloHora * $tarifaHora[0]->getValor();
        }
        if($intervaloMinuto > 0){
            if($intervaloMinuto < 16){
                $valorTemporalFraccion = $tarifaFraccion[0]->getValor();
            }else if($intervaloMinuto < 31){
                $valorTemporalFraccion = 2 * $tarifaFraccion[0]->getValor();
            }else if($intervaloMinuto < 46){
                $valorTemporalFraccion = 3 * $tarifaFraccion[0]->getValor();
            }else{
                $valorTemporalFraccion = 4 * $tarifaFraccion[0]->getValor();
            } 
        }
        return $valorTotal = $valorTemporalHora + $valorTemporalFraccion;
    }

    public function ticket_pago($data){
      $array = array();
      $ticket = $data->ticket;
      $this->_ingreso->findByObject(array('numero' => $ticket));
      if(!$this->_ingreso->getInstance()->getNumero()){
        $array['respuesta'] = false;
        $array['mensaje'] = 'No se encuentra el TICKET';
        return  json_encode($array);
      }
      $this->_pago->findByObject(array('ingreso' => $this->_ingreso->getInstance()->getId()));
      if($this->_pago->getInstance()->getId()){
        $array['respuesta'] = false;
        $array['mensaje'] = 'El TICKET ya esta pagado';
        return  json_encode($array);
      }
      $fechaEntrada = $this->_ingreso->getInstance()->getFechaIngreso();
      $fechaSalida = new \DateTime();
      $fechaIntervalo = $fechaEntrada->diff($fechaSalida);
      $totalPagar = $this->ValorPorPagar($this->_ingreso->getInstance(),$fechaIntervalo);
      $iva = $totalPagar * 0.19;
      $valor = $totalPagar - $iva;
      $this->_pago->getInstance()->setFecha(new \DateTime());
      $this->_pago->getInstance()->setValor($valor);
      $this->_pago->getInstance()->setIva($iva);
      $this->_pago->getInstance()->setEntrego(0);
      $this->_pago->getInstance()->setCambio(0);
      $this->_pago->getInstance()->setIngreso($this->_ingreso->getInstance()->getId());
      $this->_pago->getInstance()->setUsuario($this->_usuario->get(1));
      $this->_pago->getInstance()->setCaja($this->_caja->get(1));
      $this->_pago->save();
      $this->_pagoServicio->getInstance()->setId($this->_pago->getInstance());
      $this->_pagoServicio->getInstance()->setIngreso($this->_ingresoNormal->get($this->_ingreso->getInstance()->getId()));
      $this->_pagoServicio->save();
      return $this->ticket($data);
    }

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
