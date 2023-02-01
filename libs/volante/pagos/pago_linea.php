<?php

@define('DS', DIRECTORY_SEPARATOR);
@define('ROOTs', realpath(dirname(__FILE__)) . DS);

/*
+--------------------------------------------------------------------------
| Proyecto Sistema de Información
|  2015
| == == == == == == == == == == == == == == == == == == == ==
|
| Creado por el Ingenieros de Sistemas:
| Carlos René Angarita Sanguino
| == == == == == == == == == == == == == == == == == == == ==
| UNIVERSIDAD SIMON BOLIVAR
+---------------------------------------------------------------------------
|		  
*/

include(ROOTs.'..'.DS.'db.php');
require_once(ROOTs.'lib'.DS.'nusoap.php');




function IsFechaMenorIgualQueActual($fecha_control){
	//recibe yyyy-mm-dd
	//Esta funcion valida que la fecha actual sea menor de la fecha de control
	$dia=date("j");
	$mes=date("n");
	$year=date("Y");
	//descomponer fecha de nacimiento
	$diaControl=substr($fecha_control, 8, 2);
	$mesControl=substr($fecha_control, 5, 2);
	$yearControl=substr($fecha_control, 0, 4);
	
	if($year==$yearControl){
		if($mes<$mesControl){
			return true;
		}
		elseif($mes==$mesControl && $dia<=$diaControl){
			return true;
		}else{
			return false;
		}
	}
	elseif($year<$yearControl){
		return true;
	}
	else{
		return false;
	}
}




//ver si esta matriculado

/**
* 
*/
class Pagos
{
	var $bd;
	var $volante;
	var $info = false;

	function __construct()
	{
		$this->bd = Db2::getInstance('WSIAACA');
	}

	public function informacion($volante=0)
	{
		
		$valormatriculapse = 0;
		if(isset($volante) && !empty($volante) && $volante >0){
			$num_volante=$volante;
			$numvol=0;

			$sql = "sELECT 1 tipo, VOLANTES.VOLESTUDIANTE, VOLANTES.VOLUSUCRE, VOLANTES.VOLNOMBRE, VOLANTES.VOLPERANO, VOLANTES.VOLPERSEM, VOLANTES.VOLNUMERO, VOLANTES.VOLFECCRE, ESTUDIANTES.ESTIDE, VOLANTES.VOLCUENTA, BANCOS.BCONOMBRE, BANCOS.BCOCODIGO, VOLANTES.VOLPROGRAMA, PROGRAMAS.PRGNOMBRE, DOCUMENTOS.DOCNOMBRE, VOLANTES.VOLVALOR, VOLANTES.VOLVALOREX1, VOLANTES.VOLVALOREX2, VOLANTES.VOLVALOREX3, VOLANTES.VOLFECHA, VOLANTES.VOLFECHA1, VOLANTES.VOLFECHA2, VOLANTES.VOLFECHA3, VOLANTES.VOLDEPENUSO, VOLANTES.VOLDOCUMENTO, ESTUDIANTES.ESTAPELLIDO1, ESTUDIANTES.ESTAPELLIDO2, ESTUDIANTES.ESTNOMBRE1, ESTUDIANTES.ESTNOMBRE2 FROM (BANCOS INNER JOIN (((VOLANTES INNER JOIN DOCUMENTOS ON VOLANTES.VOLDOCUMENTO = DOCUMENTOS.DOCCODIGO) INNER JOIN PROGRAMAS ON VOLANTES.VOLPROGRAMA = PROGRAMAS.PRGCODPROG) INNER JOIN ESTUDIANTES ON VOLANTES.VOLESTUDIANTE = ESTUDIANTES.ESTCODIGO) ON BANCOS.BCOCODIGO = VOLANTES.VOLBANCO) WHERE VOLANTES.VOLNUMERO=$num_volante AND VOLANTES.VOLFECcre >= (CURRENT DATE - 6 MONTHS)"; 
			try{
				$result=$this->bd->ejecutar($sql);
				$numvol=$this->bd->getNumFilas();
			}catch(Exception $e){
				$numvol=0;
			}

			if($numvol == 0){
				$sql = "sELECT 0 tipo, DB2ADMIN.VOLANTEEXTERNO.VOLEXTerno VOLESTUDIANTE, DB2ADMIN.VOLANTEEXTERNO.VOLEXTDESCRIPCION VOLNOMBRE, DB2ADMIN.VOLANTEEXTERNO.VOLextPERANO VOLPERANO, DB2ADMIN.VOLANTEEXTERNO.VOLextPERSEM VOLPERSEM, DB2ADMIN.VOLANTEEXTERNO.VOLextnumero VOLNUMERO, DB2ADMIN.VOLANTEEXTERNO.VOLextfeccre VOLFECCRE, DB2ADMIN.VOLANTEEXTERNO.VOLexterno ESTIDE, DB2ADMIN.VOLANTEEXTERNO.VOLextcuenta VOLCUENTA, DB2ADMIN.VOLANTEEXTERNO.VOLextUSUCRE VOLUSUCRE, db2admin.bancos.BCONOMBRE, db2admin.bancos.BCOCODIGO, DB2ADMIN.VOLANTEEXTERNO.VOLEXTPROGRAMA VOLPROGRAMA, '' PRGNOMBRE, DB2ADMIN.DOCUMENTOS.DOCNOMBRE, DB2ADMIN.VOLANTEEXTERNO.VOLextVALOR volvalor, DB2ADMIN.VOLANTEEXTERNO.VOLextVALOR VOLVALORex1, DB2ADMIN.VOLANTEEXTERNO.VOLextVALOR VOLVALORex2, DB2ADMIN.VOLANTEEXTERNO.VOLextVALOR VOLVALORex3, DB2ADMIN.VOLANTEEXTERNO.VOLextFECHA VOLFECHA, DB2ADMIN.VOLANTEEXTERNO.VOLextFECHA VOLFECHA1, DB2ADMIN.VOLANTEEXTERNO.VOLextFECHA VOLFECHA2, DB2ADMIN.VOLANTEEXTERNO.VOLextFECHA VOLFECHA3, DB2ADMIN.VOLANTEEXTERNO.VOLEXTDEPENUSO VOLDEPENUSO, DB2ADMIN.VOLANTEEXTERNO.VOLEXTDOCUMENTO VOLDOCUMENTO, DB2ADMIN.EXTERNOS.EXTAPELLIDO1 ESTAPELLIDO1, DB2ADMIN.EXTERNOS.EXTAPELLIDO2 ESTAPELLIDO2, DB2ADMIN.EXTERNOS.EXTNOMBRE1 ESTNOMBRE1, DB2ADMIN.EXTERNOS.EXTNOMBRE2 ESTNOMBRE2 FROM DB2ADMIN.DOCUMENTOS INNER JOIN (DB2ADMIN.EXTERNOS INNER JOIN DB2ADMIN.VOLANTEEXTERNO ON DB2ADMIN.EXTERNOS.EXTIDE = DB2ADMIN.VOLANTEEXTERNO.VOLEXTERNO) ON DB2ADMIN.DOCUMENTOS.DOCCODIGO = DB2ADMIN.VOLANTEEXTERNO.VOLEXTDOCUMENTO inner join db2admin.bancos ON db2admin.BANCOS.BCOCODIGO = DB2ADMIN.VOLANTEEXTERNO.VOLextBANCo WHERE DB2ADMIN.VOLANTEEXTERNO.VOLextNUMERO=$num_volante AND DB2ADMIN.VOLANTEEXTERNO.VOLextFECcre >= (CURRENT DATE - 6 MONTHS)"; 
				try{
					$result=$this->bd->ejecutar($sql);
					$numvol=$this->bd->getNumFilas();
				}catch(Exception $e){
					$numvol=0;
				}
			}

			if($numvol!=0){

				$rta = $this->verificar($volante);
				## && $rta['idestado']=='1'
				if($rta){
					$this->info=array('error' => 'Volante ('.$volante.') transacci&oacute;n en linea con estado ('.$rta['Estado'].').');
					return $this->info;
					exit;
				}

				$campo=$this->bd->getResult($result);
				$valor = $campo['VOLVALOR'];
				$tipo = $campo['TIPO'];
				$usuario = $campo['VOLUSUCRE'];
				$descripcion = $campo['VOLNOMBRE'];
				$nombre = $campo['ESTNOMBRE1'].' '.$campo['ESTNOMBRE2'];
				$apellido = $campo['ESTAPELLIDO1'].' '.$campo['ESTAPELLIDO2'];
				$codigo = $campo['VOLESTUDIANTE'];
				$volnombre =  $campo['DOCNOMBRE'];

				$this->bd->getFreeResult($result);

				$QuitarFecha1=$QuitarFecha2=$QuitarFecha3=$QuitarFecha4=true;
				$cod_valor4=$cod_fecha4=$cod_fecha3=$cod_fecha2=$cod_fecha1='';

				if($campo['VOLFECHA']<>'0001-01-01'){
					if($campo['VOLFECHA']<>'1900-01-01'){
						if(IsFechaMenorIgualQueActual($campo['VOLFECHA'])){
							$cod_fecha1 = $campo['VOLFECHA'];
							$QuitarFecha1=false;
						}
					}
				}
				if($campo['VOLFECHA1']<>'0001-01-01'){
					if($campo['VOLFECHA1']<>'1900-01-01'){
						if(IsFechaMenorIgualQueActual($campo['VOLFECHA1'])){
							$cod_fecha2 = $campo['VOLFECHA1'];
							$QuitarFecha2=false;
						}
					}
				}
				if($campo['VOLFECHA2']<>'0001-01-01'){
					if($campo['VOLFECHA2']<>'1900-01-01'){
						if(IsFechaMenorIgualQueActual($campo['VOLFECHA2'])){
							$cod_fecha3 = $campo['VOLFECHA2'];
							$QuitarFecha3=false;
						}
					}
				}

				if($campo['VOLFECHA3']<>'0001-01-01'){
					if($campo['VOLFECHA3']<>'1900-01-01'){
						if(IsFechaMenorIgualQueActual($campo['VOLFECHA3'])){
							$cod_fecha4 = $campo['VOLFECHA3'];
							$QuitarFecha4=false;
						}
					}
				}
				if($QuitarFecha1 && $QuitarFecha2 && $QuitarFecha3 && $QuitarFecha4){
					$this->info=array('error'=>'Las Fechas de pago estan vencidas, por este motivo no se puede generar este volante de pago.');
					return $this->info;
					exit;
				}

				$cod_valor1=$campo["VOLVALOR"];
				$cod_valor2=$campo['VOLVALOREX1'];
				$cod_valor3=$campo['VOLVALOREX2'];
				$cod_valor4=$campo['VOLVALOREX3'];


				if($cod_fecha1<>'' && $valormatriculapse == 0){
					$valormatriculapse =  $campo["VOLVALOR"];
				}
				if($cod_fecha2<>'' && $valormatriculapse == 0){
					$valormatriculapse =  $campo["VOLVALOREX1"];
				}
				if($cod_fecha3<>'' && $valormatriculapse == 0){
					$valormatriculapse =  $campo["VOLVALOREX2"];
				}
				if($cod_fecha4<>'' && !$cod_fecha1<>'' && $cod_valor4 > $cod_valor3 && $valormatriculapse == 0){
					$valormatriculapse =  $campo["VOLVALOREX3"];
				}

			}

			if($valormatriculapse==0){
				$this->info = array('error'=>'Volante no encontrado');
				return $this->info;
			}

			$this->info = array('error'=>false,'documento'=>$volnombre,'tipo'=>$tipo,'descripcion'=>$descripcion,'usuario'=>$usuario,'id'=>$codigo,'numero'=>$volante,'valor' => $valormatriculapse, 'persona'=>array('apellido'=>$apellido,'nombre'=>$nombre) );
			return $this->info;
			
		}


		$this->info = array('error'=>'error de campos');
		return $this->info;

	}

	public function post()
	{
		if(!$this->info || $this->info['error'] || $this->info['error']!=''){
			return array('error' => 'falta la informacion');
		}

		$des=$this->info['documento'];
		$op1='';$op2='';$op3='';
		if($this->info['usuario']=='WEB-VOL'){
			$op1 = substr(utf8_encode($this->info['descripcion']), 0, 70);
			$op2 = substr(utf8_encode($this->info['descripcion']), 70, 140);
			$op3 = substr(utf8_encode($this->info['descripcion']), 140, 200);
		}

		try {
			$client = new SoapClient("https://www.zonapagos.com/ws_inicio_pagov2/Zpagos.asmx?WSDL",
				array('cache_wsdl' => WSDL_CACHE_NONE,'trace' => TRUE));
			$param = array("id_tienda" =>"10798", 
				"clave" =>"usbolivar10798", 
				"total_con_iva" =>$this->info['valor'] , 
				"valor_iva" =>"0", 
				"id_pago" =>$this->info['numero'], 
				"descripcion_pago" =>$des, 
				"email" =>$_POST['ESTEMAIL'],
				"id_cliente" =>$this->info['id'],
				"tipo_id" =>"1",
				"nombre_cliente" =>utf8_encode($this->info['persona']['nombre']), 
				"apellido_cliente" =>utf8_encode($this->info['persona']['apellido']), 
				"telefono_cliente" =>$_POST['ESTTELEFONO'], 
				"info_opcional1" =>$op1, 
				"info_opcional2" =>$op2, 
				"info_opcional3" =>$op3,
				"codigo_servicio_principal" =>"1017",
				"lista_codigos_servicio_multicredito" =>array("0"),
				"lista_nit_codigos_servicio_multicredito" =>array("0"),
				"lista_valores_con_iva" =>array(0),
				"lista_valores_iva" =>array(0),
				"total_codigos_servicio" =>"0");

			$ready = $client->inicio_pagoV2($param)->inicio_pagoV2Result;


			header("location: https://www.zonapagos.com/t_usbolivar/pago.asp?estado_pago=iniciar_pago&identificador=".$ready);
			exit;

		} catch (Exception $e) {
			return array('error' =>  $e->getMessage());
		} 

	}

	public function verificar($volante=0)
	{
		try {
			$client = new SoapClient("https://www.zonapagos.com/ws_verificar_pagos/Service.asmx?WSDL",
				array('cache_wsdl' => WSDL_CACHE_NONE,'trace' => TRUE));
			$param = array(
				"str_id_pago" => $volante,
				"int_id_tienda" =>"10798", 
				"str_id_clave" =>"usbolivar10798", 
				"res_pagos_v3" =>array("0"),
				"int_error" =>"0", 
				"str_error" =>"");

			$readyser = $client->verificar_pago_v3($param);
			$ready = $readyser->verificar_pago_v3Result;
			$interror = $readyser->int_error;
			$strerror = $readyser->str_error;
			if($interror==0){
				$ready = $readyser->res_pagos_v3;
				$ready = $ready->pagos_v3;



				$fechaPago = $ready->dat_fecha;
				$valor = number_format($ready->dbl_valor_pagado,0,".",",");
				$nombanco = $ready->str_nombre_banco;
				$transferencia = $ready->str_codigo_transaccion;

				if($ready->int_estado_pago == 888){
					$estado = "Pago pendiente por iniciar";
				}else if($ready->int_estado_pago == 999){
					$estado = "Pago pendiente por finalizar";
				}else if($ready->int_estado_pago == 4001){
					$estado = "Pendiente por CR";
				}else if($ready->int_estado_pago == 1){
					$estado = "Pago finalizado";
				}

				return array(
					'Fecha'=>$fechaPago,
					'Valor'=>$valor,
					'Banco'=>$nombanco,
					'Transferencia'=>$transferencia,
					'Estado'=>$estado,
					'idestado' =>$ready->int_estado_pago
					);
			}




		} catch (Exception $e) {
				//echo("Error");
				//echo($e->getMessage());
				//trigger_error($e->getMessage(), E_USER_WARNING);
		} 
	}


}
?>