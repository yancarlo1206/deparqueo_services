<?php 
//echo("333");
include('includes/sys_funciones.php');
require_once('modulos/estudiante/lib/nusoap.php');
$volante = $_POST['dato'];
//$volante = 28148209;
$cad = "";
$interror = 99;
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
					
					//var_dump($param); 
			 
				$readyser = $client->verificar_pago_v3($param);
				$ready = $readyser->verificar_pago_v3Result;
				$interror = $readyser->int_error;
				$strerror = $readyser->str_error;
				if($interror==0){
					$ready = $readyser->res_pagos_v3;
					//var_dump($ready);
					$ready = $ready->pagos_v3;
					
					
					//var_dump($ready); //Verificar si hay resultado
					
					//$cad = $strerror;

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
						$estado = "Pago finalizado Ok";
					}

				}
				
			} catch (Exception $e) {
				//echo("Error");
				//echo($e->getMessage());
				//trigger_error($e->getMessage(), E_USER_WARNING);
				
			} 



if($interror == 0) {

$cad .='<table id="tbestado" width="100%" border="1" cellpadding="1" cellspacing="1" class="tablabordesexternos">

<tr>
  <th width="30%" align="left" class="TituloTabla"><strong>Numero Volante</strong></th>
  <td width="70%" bgcolor="#F9F9F9" class="tablabordesdefilas">&nbsp;'.$volante.'</td>
</tr>

<tr>
  <th width="30%" align="left" class="TituloTabla"><strong>Fecha Pago</strong></th>
  <td width="70%" bgcolor="#F9F9F9" class="tablabordesdefilas">&nbsp;'.$fechaPago.'</td>
</tr>

<tr>
  <th width="30%" align="left" class="TituloTabla"><strong>Valor</strong></th>
  <td width="70%" bgcolor="#F9F9F9" class="tablabordesdefilas">&nbsp;$ '.$valor.'</td>
</tr>

<tr>
  <th width="30%" align="left" class="TituloTabla"><strong>Nombre Banco</strong></th>
  <td width="70%" bgcolor="#F9F9F9" class="tablabordesdefilas">&nbsp;'.$nombanco.'</td>
</tr>

<tr>
  <th width="30%" align="left" class="TituloTabla"><strong>CUS Transacción</strong></th>
  <td width="70%" bgcolor="#F9F9F9" class="tablabordesdefilas">&nbsp;'.$transferencia.'</td>
</tr>

<tr>
  <th width="30%" align="left" class="TituloTabla"><strong>Estado</strong></th>
  <td width="70%" bgcolor="#F9F9F9" class="tablabordesdefilas">&nbsp;'.$estado.'</td>
</tr>                                                           
</table>';
echo $cad;
exit();
}else{
	?>
	<div class="mensaje_error" >
       <img height="16" src="imagenes/icon_error.gif"width="16" /> <?php echo (explode(".",$strerror)[0]. " para el documento con numero ".$volante); ?>                    
    </div>
<?php
}
?>

