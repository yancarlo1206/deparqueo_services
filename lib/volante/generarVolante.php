<?php
//include('../../sys_seguridad.php');
include('volante.php');

$volante = new Volante(); 
$codEstudiante ='201422211590';// $_POST['codigo'];
$codDocumento = '2';//$_POST['coddoc'];
$codPrograma ='915';// $_POST['prog'];
$codSemestre ='1';// $_POST['sem'];
// SE CALCULA LA FECHA DE PAGO///
$fecha = date('Y-m-d');
$fechavecimiente = strtotime('+3 day' ,strtotime ($fecha));
$fechaPago = date('Y-m-d',$fechavecimiente);
$valor = $volante->valoresdoc($codPrograma,$codDocumento,$codSemestre);
$valorDocumento = $valor['VLSVALOR'];
$valorDocumento1 = $valor['VLSVALOR'];
$mensj='';
/*if($codDocumento==2){
 
$porciento = $valorDocumento*10/100 ;
$valorDocumento=$valorDocumento-$porciento;
$mensj= 'Descuento Por Pago Anticipado del 10% ';

}*/

//$valorDocumento=200000;
$arrayDatos = array(
                     'estudiante' => $codEstudiante,                        
                     'documento' => $codDocumento,                                  
                     'banco' => true,
                     'valores' => array(
                                        $valorDocumento
                                        ), 
                     'fechas' => array(
                                        $fechaPago
                                       ),                                  
                     'imprimir' => false,                                   
                     'descripcion' =>$mensj
                    );
//echo 'xxxx xxx ';				   
$num = $volante->generar($arrayDatos);
echo $num;
//echo 'xxxx xxx ';
exit;
?>