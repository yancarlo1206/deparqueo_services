<?php

error_reporting(1);
ini_set('display_errors', '1');

include_once ("application" . DS . "Controller.php");

class ticketController extends Controller {

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
        $this->_tipoSancion = $this->loadModel('tiposancion');
    }

    public function index() { }

    public function consulta_pago($data){
    	$sql = "SELECT usuario.usuario, SUM(pago.valor+pago.iva) AS total, COUNT(pago.id) AS cantidad 
        FROM pago INNER JOIN usuario ON usuario.id = pago.usuario INNER JOIN rol ON rol.id = usuario.rol
        WHERE DATE(fecha) = CURDATE() AND rol.id=4
        GROUP BY usuario.usuario";
    	$temp =	$this->_pago->nativeQuery($sql);
    	$arrayInt = array();
    	$array = array();
    	foreach ($temp as $key => $value) {
    	 $arrayInt['usuario'] = $value['usuario'];
    	 $arrayInt['cantidad'] = $value['cantidad'];
    	 $arrayInt['total'] = $value['total'];
        $array[] = $arrayInt;
      }
      return  json_encode($array);
    }

    public function ticket($data){
      $array = array();
      $ticket = $data->ticket;
      $this->_ingreso->findByObject(array('numero' => $ticket));
      //$this->_pago->findByObject(array('ingreso' => $this->_ingreso->getInstance()->getId()));
      $pagos = $this->_pago->findBy(array('ingreso' => $this->_ingreso->getInstance()->getId()));
      if(!$this->_ingreso->getInstance()->getNumero()){
        $array['respuesta'] = false;
        $array['mensaje'] = 'No se encuentra el TICKET';
        return  json_encode($array);
      }
      $this->_ingresoNormal->get($this->_ingreso->getInstance()->getId());
      if(count($this->_ingresoNormal->getInstance()->getCancelados())){
	      $array['respuesta'] = false;
        $array['mensaje'] = 'El TICKET esta Cancelado';
        return  json_encode($array);
      }
      $array['respuesta'] = true;
      $array['ticket'] = $this->_ingreso->getInstance()->getNumero();
      $array['fecha'] = $this->_ingreso->getInstance()->getFecha()->format('d-m-Y');
      $array['tipoVehiculo'] = $this->_ingreso->getInstance()->getTipo()->getDescripcion();
      $array['fechaEntrada'] = $this->_ingreso->getInstance()->getFechaIngreso()->format('d-m-Y H:i');
      if($this->_ingreso->getInstance()->getFechaSalida()){
        $array['fechaSalida'] = $this->_ingreso->getInstance()->getFechaSalida()->format('d-m-Y H:i');
      }
      if($this->_ingreso->getInstance()->getCasco() > 0){
        $array['casco'] = $this->_ingreso->getInstance()->getCasco();
      }
      if(count($pagos)){
        $fechaEntrada = $this->_ingreso->getInstance()->getFechaIngreso();
        $valorPagado = $pagos[0]->getValor() + $pagos[0]->getIva();
        $valorTarifa = $this->calcular_tarifa( $this->_ingreso->getInstance());
        $fechaPago = $pagos[0]->getFecha();
        $this->_variable->get(2);
        $vencido = $this->_variable->getInstance()->getValor();
        //$minutosLimite = (($valorPagado / $valorTarifa) * 15);
        $minutosLimite = $vencido;
        $fechaLimiteNoVencida = $fechaPago->modify('+'.$minutosLimite.' minute');
        $fechaLimite = $fechaEntrada->modify('+'.$minutosLimite+$vencido.' minute');
        $fechaLimiteComparar = $fechaLimite;
        $fechaActualComparar = new \DateTime();
        if($fechaLimiteNoVencida < $fechaActualComparar){
          $fechaEntrada = $fechaLimiteNoVencida;
          $fechaSalida = new \DateTime();
          $fechaIntervalo = $fechaEntrada->diff($fechaSalida);
          $array['pagoAdicional'] = 1;
          $array['valorPorPagar'] = $this->ValorPorPagar($this->_ingreso->getInstance(),$fechaIntervalo);
          return  json_encode($array);
        }
      }
      if(count($pagos)){
        $valorPagado = 0;
        $valor = 0;
        $iva = 0;
        $entrego = 0;
        $cambio = 0;
        foreach ($pagos as $key => $value) {
          $factura = $value->getFactura();
          $valorPagado = $valorPagado + $value->getValor() + $value->getIva();
          $valor = $valor +  $value->getValor();
          $iva = $iva + $value->getIva();
          $entrego = $entrego + $value->getEntrego();
          $cambio = $cambio + $value->getCambio();
        }
        $array['facturaVenta'] = $factura;
        $array['valorPagado'] = $valorPagado;
        $array['valor'] = $valor;
        $array['iva'] = $iva;
        $array['entrego'] = $entrego;
        $array['cambio'] = $cambio;
      }else{
        $fechaEntrada = $this->_ingreso->getInstance()->getFechaIngreso();
        $fechaSalida = new \DateTime();
        $fechaIntervalo = $fechaEntrada->diff($fechaSalida);
        $valorAdicional = 0;
        if($this->_ingreso->getInstance()->getCasco() > 0){
          $this->_tipoSancion->get(3);
          $valorAdicional = $this->_tipoSancion->getInstance()->getValor() * $this->_ingreso->getInstance()->getCasco();
        }
        $array['valorPorPagar'] = $this->ValorPorPagar($this->_ingreso->getInstance(),$fechaIntervalo) + $valorAdicional;
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
      $totalPagar = $data->valorPorPagar;
      $rfid = $data->rfid;
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
      $this->_usuario->findByObject(array('rfid' => $rfid));
      $fechaEntrada = $this->_ingreso->getInstance()->getFechaIngreso();
      $fechaSalida = new \DateTime();
      $fechaIntervalo = $fechaEntrada->diff($fechaSalida);
      //$totalPagar = $this->ValorPorPagar($this->_ingreso->getInstance(),$fechaIntervalo);
      if($this->_ingreso->getInstance()->getCasco() > 0){
        $this->_tipoSancion->get(3);
        $valorAdicional = $this->_tipoSancion->getInstance()->getValor() * $this->_ingreso->getInstance()->getCasco();
        $totalPagar = $totalPagar - $valorAdicional;
      }
      $baseGrabable = round($totalPagar / 1.19);
      $iva = $totalPagar - $baseGrabable;
      $valor = $totalPagar - $iva;
      $this->_pago->getInstance()->setFecha(new \DateTime());
      $this->_pago->getInstance()->setValor($valor);
      $this->_pago->getInstance()->setIva($iva);
      $this->_pago->getInstance()->setEntrego(0);
      $this->_pago->getInstance()->setCambio(0);
      $this->_pago->getInstance()->setIngreso($this->_ingreso->getInstance()->getId());
      $this->_pago->getInstance()->setUsuario($this->_usuario->getInstance());
      $this->_pago->getInstance()->setCaja($this->_caja->get(2));
      $this->_variable->get(1);
      $consecutivo = $this->_variable->getInstance()->getValor();
      $this->_pago->getInstance()->setFactura($consecutivo);
      $this->_pago->save();
      $this->_pagoServicio->getInstance()->setId($this->_pago->getInstance());
      $this->_pagoServicio->getInstance()->setIngreso($this->_ingresoNormal->get($this->_ingreso->getInstance()->getId()));
      $this->_pagoServicio->getInstance()->setAdicional(0);
      $this->_pagoServicio->save();
      if($this->_ingreso->getInstance()->getCasco() > 0){
        $this->_pago = $this->loadModel('pago');
        $baseGrabable = round($valorAdicional / 1.19);
        $iva = $valorAdicional - $baseGrabable;
        $valor = $valorAdicional - $iva;
        $this->_pago->getInstance()->setFecha(new \DateTime());
        $this->_pago->getInstance()->setValor($valor);
        $this->_pago->getInstance()->setIva($iva);
        $this->_pago->getInstance()->setEntrego(0);
        $this->_pago->getInstance()->setCambio(0);
        $this->_pago->getInstance()->setIngreso($this->_ingreso->getInstance()->getId());
        $this->_pago->getInstance()->setUsuario($this->_usuario->getInstance());
        $this->_pago->getInstance()->setCaja($this->_caja->get(2));
        $this->_pago->getInstance()->setFactura($consecutivo);
        $this->_pago->save();
        $this->_pagoSancion->getInstance()->setId($this->_pago->getInstance());
        $this->_pagoSancion->getInstance()->setDocumento(0);
        $this->_pagoSancion->getInstance()->setFecha(new \DateTime());
        $this->_pagoSancion->getInstance()->setTipoSancion($this->_tipoSancion->get(3));
        $this->_pagoSancion->save();
      }
      $consecutivo = $consecutivo+1;
      $this->_variable->getInstance()->setValor($consecutivo);
      $this->_variable->update();
      return $this->ticket($data);
    }

    public function ticket_pago_adicional($data){
      $array = array();
      $ticket = $data->ticket;
      $totalPagar = $data->valorPorPagar;
      $rfid = $data->rfid;
      $this->_ingreso->findByObject(array('numero' => $ticket));
      if(!$this->_ingreso->getInstance()->getNumero()){
        $array['respuesta'] = false;
        $array['mensaje'] = 'No se encuentra el TICKET';
        return  json_encode($array);
      }
      $this->_usuario->findByObject(array('rfid' => $rfid));
      $baseGrabable = round($totalPagar / 1.19);
      $iva = $totalPagar - $baseGrabable;
      $valor = $totalPagar - $iva;
      $this->_pago->getInstance()->setFecha(new \DateTime());
      $this->_pago->getInstance()->setValor($valor);
      $this->_pago->getInstance()->setIva($iva);
      $this->_pago->getInstance()->setEntrego(0);
      $this->_pago->getInstance()->setCambio(0);
      $this->_pago->getInstance()->setIngreso($this->_ingreso->getInstance()->getId());
      $this->_pago->getInstance()->setUsuario($this->_usuario->getInstance());
      $this->_pago->getInstance()->setCaja($this->_caja->get(2));
      $this->_variable->get(1);
      $consecutivo = $this->_variable->getInstance()->getValor();
      $this->_pago->getInstance()->setFactura($consecutivo);
      $this->_pago->save();
      $this->_pagoServicio->getInstance()->setId($this->_pago->getInstance());
      $this->_pagoServicio->getInstance()->setIngreso($this->_ingresoNormal->get($this->_ingreso->getInstance()->getId()));
      $this->_pagoServicio->getInstance()->setAdicional(1);
      $this->_pagoServicio->save();
      $consecutivo = $consecutivo+1;
      $this->_variable->getInstance()->setValor($consecutivo);
      $this->_variable->update();
      return $this->ticket($data);
    }

    public function calcular_tarifa($ingreso=null){
      $tarifaFraccion = $this->_tarifa->dql("SELECT t FROM Entities\Tarifa t 
        WHERE t.fechainicio <=:fecha AND t.fechafin >=:fecha 
        AND t.tipovehiculo =:tipoVehiculo AND t.tipotarifa = 2",
        array('fecha' => new \DateTime(), 'tipoVehiculo' => $ingreso->getTipo()->getId()));
      return $tarifaFraccion[0]->getValor();
    }

}

?>
