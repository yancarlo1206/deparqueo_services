<?php
/*
include('../sys_seguridad.php');
include('../includes/Conf.class.php');
include('../includes/Db.class.php');
include('../includes/sys_funciones.php');
include('../includes/IsFechaMenorIgualQueActual.php');
session_start();
/*if($_SESSION["imprimirvolante"]!= "ce2b48c3169fbbe341e1e69ba9c4441c"){
	session_destroy();
	header("location:index.php");
	exit();
}*/
function ListaBancos($CodIAC,$CodBanco){
	$bd=Db::getInstance();
 $sql=" Select BCORESUMEN from DB2ADMIN.bancos, DB2ADMIN.cuentas  
	where 
	bcoCodigo = ctaBanco 
	and CtaCodIac ='$CodIAC'
	order By BcoOrden";
	$resultFun=$bd->ejecutar($sql);
  $numFun=$bd->getNumFilas();
	$ListaBancos='';
	if($numFun){
	  while($campoFun=$bd->getResult($resultFun)){
				$ListaBancos=$ListaBancos.$campoFun['BCORESUMEN']." | ";
		}
		return $ListaBancos;
	}
}

$bd=Db::getInstance();
if(isset($_GET['volante']) && !empty($_GET['volante'])){
    $num_volante=$_GET['volante'];
    $tod_volante='S';
    if(isset($_GET['todos'])) $tod_volante=$_GET['todos'];
    $sql="SELECT
		VOLANTES.VOLESTUDIANTE, 
		VOLANTES.VOLPERANO, 
		VOLANTES.VOLPERSEM, 
		VOLANTES.VOLNUMERO,
		VOLANTES.VOLFECCRE, 
		ESTUDIANTES.ESTIDE, 
		VOLANTES.VOLCUENTA, 
		BANCOS.BCONOMBRE, 
		BANCOS.BCOCODIGO, 
		VOLANTES.VOLPROGRAMA, 
		PROGRAMAS.PRGNOMBRE, 
		DOCUMENTOS.DOCNOMBRE, 
		VOLANTES.VOLVALOR, 
		VOLANTES.VOLVALOREX1, 
		VOLANTES.VOLVALOREX2, 
		VOLANTES.VOLVALOREX3,
		VOLANTES.VOLFECHA, 
		VOLANTES.VOLFECHA1, 
		VOLANTES.VOLFECHA2, 
		VOLANTES.VOLFECHA3, 
		VOLANTES.VOLDEPENUSO,
		VOLANTES.VOLDOCUMENTO,
		ESTUDIANTES.ESTAPELLIDO1, 
		ESTUDIANTES.ESTAPELLIDO2, 
		ESTUDIANTES.ESTNOMBRE1, 
		ESTUDIANTES.ESTNOMBRE2, 
		CUENTAS.CTACODIAC
	FROM 
		(BANCOS INNER JOIN (((VOLANTES INNER JOIN DOCUMENTOS ON VOLANTES.VOLDOCUMENTO = DOCUMENTOS.DOCCODIGO) 
		INNER JOIN PROGRAMAS ON VOLANTES.VOLPROGRAMA = PROGRAMAS.PRGCODPROG) 
		INNER JOIN ESTUDIANTES ON VOLANTES.VOLESTUDIANTE = ESTUDIANTES.ESTCODIGO) ON BANCOS.BCOCODIGO = VOLANTES.VOLBANCO) 
		INNER JOIN CUENTAS ON (VOLANTES.VOLBANCO = CUENTAS.CTABANCO) 
		AND (VOLANTES.VOLTIPCUENTA = CUENTAS.CTATIPCUENTA) 
		AND (VOLANTES.VOLCUENTA = CUENTAS.CTACUENTA)
	WHERE 
		VOLANTES.VOLNUMERO=$num_volante";
    try{
        $result=$bd->ejecutar($sql);
        $numvol=$bd->getNumFilas();
    }catch(Exception $e){print 'Error al obtener datos'; $numvol=0;}
	if($numvol){
		$campo=$bd->getResult($result);
		 $valor       = $campo['VOLVALOR'];
		 $periodo_year= $campo['VOLPERANO'];
		 $periodo_sem= $campo['VOLPERSEM'];
		 $nro_ref     = $num_volante;
		  $banco    = $campo['BCONOMBRE'];
		  $cuenta   = $campo['VOLCUENTA'];
		  $fecha    = fechaddmmaaaa($campo['VOLFECCRE']);//date ('d/m/Y');
		  $identif  = $campo['ESTIDE'];
		  $periodo  = $periodo_year.'-'.$periodo_sem;
		  $nombre   = $campo['ESTAPELLIDO1'].' '.$campo['ESTAPELLIDO2'].' '.$campo['ESTNOMBRE1'].' '.$campo['ESTNOMBRE2'];
		  $codigo   = $campo['VOLESTUDIANTE'];
		  $programa = $campo['PRGNOMBRE'];
		 	$bd->getFreeResult($result);
			//fecha y valor para cod barra
			$QuitarFecha1=$QuitarFecha2=$QuitarFecha3=$QuitarFecha4=true;
			$cod_valor4=$cod_fecha4=$cod_fecha3=$cod_fecha2=$cod_fecha1='';
			if($campo['VOLFECHA']<>'0001-01-01'){
				if($campo['VOLFECHA']<>'1900-01-01'){
					 if(IsFechaMenorIgualQueActual($campo['VOLFECHA'])){
								$f=explode('-',$campo['VOLFECHA']);
								$ano=$f[0];
								$mes=$f[1];
								$dia=$f[2];
								$cod_fecha1=$ano.$mes.$dia;
								$QuitarFecha1=false;
					 }else{
						 $QuitarFecha1=true; 
					 }
				}
			}
			if($campo['VOLFECHA1']<>'0001-01-01'){
				 if($campo['VOLFECHA1']<>'1900-01-01'){
					 if(IsFechaMenorIgualQueActual($campo['VOLFECHA1'])){
							 $f=explode('-',$campo['VOLFECHA1']);
							 $ano=$f[0];
							 $mes=$f[1];
							 $dia=$f[2];
							 $cod_fecha2=$ano.$mes.$dia;
							 $QuitarFecha2=false;
					 }else{
						$QuitarFecha2=true; 
						}
				 }
			}
			if($campo['VOLFECHA2']<>'0001-01-01'){
					if($campo['VOLFECHA2']<>'1900-01-01'){
							if(IsFechaMenorIgualQueActual($campo['VOLFECHA2'])){
									$f=explode('-',$campo['VOLFECHA2']);
									$ano=$f[0];
									$mes=$f[1];
									$dia=$f[2];
									$cod_fecha3=$ano.$mes.$dia;
									$QuitarFecha3=false;
							}else{
								$QuitarFecha3=true; 
								}
					}
			}
			if($campo['VOLFECHA3']<>'0001-01-01'){
					if($campo['VOLFECHA3']<>'1900-01-01'){
						if(IsFechaMenorIgualQueActual($campo['VOLFECHA3'])){
								$f=explode('-',$campo['VOLFECHA3']);
								$ano=$f[0];
								$mes=$f[1];
								$dia=$f[2];
								$cod_fecha4=$ano.$mes.$dia;
								$QuitarFecha4=false;
						}else{
							$QuitarFecha4=true; 
							}
					}
			}
			if($QuitarFecha1 && $QuitarFecha2 && $QuitarFecha3 && $QuitarFecha4){
			print "<script>alert('Las Fechas de pago estan vencidas, por este motivo no se puede generar este volante de pago, informar a dsrt@unisimonbolivar.edu.co');</script>";
			exit;
			}
				
		   $cod_valor1=$campo["VOLVALOR"];
		   $cod_valor2=$campo['VOLVALOREX1'];
		   $cod_valor3=$campo['VOLVALOREX2'];
		   $cod_valor4=$campo['VOLVALOREX3'];
		   $cod_iac=trim($campo['CTACODIAC']);
			 $cod_banco=$campo['BCOCODIGO'];
			 $ListaBancos=ListaBancos($cod_iac,$cod_banco);
				

		  //las fechas de pago van en un vector
		  if($cod_fecha1<>''){
			  $pago['fecha']='Ordinaria                '.fechaddmmaaaa($campo['VOLFECHA']);
			  $pago['valor']=number_format($campo["VOLVALOR"],0,".",",");
			  $pagos['pago1']=$pago;
		  }
		  if($cod_fecha2<>''){
			  $pago['fecha']='Extra Ordinaria I     '.fechaddmmaaaa($campo['VOLFECHA1']);
			  $pago['valor']=number_format($campo['VOLVALOREX1'],0,".",",");
			  $pagos['pago2']=$pago;
		  }
		  if($cod_fecha3<>''){
			  $pago['fecha']='Extra Ordinaria II    '.fechaddmmaaaa($campo['VOLFECHA2']);
			  $pago['valor']=number_format($campo['VOLVALOREX2'],0,".",",");
			  $pagos['pago3']=$pago;
		  }
		  if($cod_fecha4<>''){
			  $pago['fecha']='Extra Ordinaria III   '.fechaddmmaaaa($campo['VOLFECHA3']);
			  $pago['valor']=number_format($campo['VOLVALOREX3'],0,".",",");
			  $pagos['pago4']=$pago;
		  }
		
		
		  //consultar si el volante tiene detalles
                  
  		  $sql="SELECT DTVNUMDOCUMENTO, DTVVALOR,DTVDESCRIPCION,DTVSIGNO
                      FROM DETALLE_VOLANTES
                      WHERE DETALLE_VOLANTES.DTVVOLNUMERO=$nro_ref
                      ORDER BY DETALLE_VOLANTES.DTVITEM";
		  	$valordetalle=0;
                        try{
                            $result=$bd->ejecutar($sql);
                            $numdet=$bd->getNumFilas();
                        }catch(Exception $e){print 'Problema al obtener datos'; $numvol=0;}
			if(isset($numdet) && $numdet>0){
				while($campodetalle=$bd->getResult($result)){
					$pago['concepto']=$campodetalle['DTVSIGNO'].'  '.$campodetalle['DTVDESCRIPCION'];
                    $pago['valor']=number_format($campodetalle['DTVVALOR'],0,".",",");
			  		$conceptos[]=$pago;
				}
				$bd->getFreeResult($result);
			}

			
			
		$observacion1='1. Para pagar este Volante tener en cuenta el Total a Pagar.';
		$observacion2='2. Estudiante por favor verifique su estado academico';
		$observacion21='  para saber el Semestre a matricular.';
		$DatosBanco="BANCO: ".$banco.' CUENTA N° '.$cuenta;
	}
  error_reporting (E_ALL);  // remove this from Production Environment

  */

$nro_ref = '123456';
$banco = 'davivienda';
$cuenta = '123456789';
$fecha = '12-12-12';
$identif = '1';
$codigo  = '1150059';
$periodo  = '2';
$nombre = 'cleiver';
$programa  = 'ing sistemas';

$observacion1='1. Para pagar este Volante tener en cuenta el Total a Pagar.';
$observacion2='2. Estudiante por favor verifique su estado academico';
$observacion21='  para saber el Semestre a matricular.';
$DatosBanco="BANCO: ".$banco.' CUENTA N° '.$cuenta;


$pagos = array('pago1'=> array('valor' => '12000','fecha'=>'12/12/12') );
$tod_volante='2';

$ListaBancos = 'b,d,s,';
$cod_valor1 = '123456977';
$cod_fecha1 = '12-12-12';
$pago1  = '12';

$cod_iac ='1234645645';

  require('pdfb/pdfb.php'); // Must include this
  //require('barcode/barcode.inc.php');

  // Recommended way to use PDFB Library
  // - create your own PDF class
  // - instantiate it wherever necessary
  // - you can create multiple classes extending from PDFB
  //   for each different report

  class PDF extends PDFB
  {
    function Header()
    {
      // Add your code here to generate Headers on every page
    }

    function Footer()
    {
      // Replace this with your code to generate Footers on every page

      // PDFB Library made this dynamic PDF :)
       //Remember to use '$this->' instead of '$pdf->'
      //$this->Text(402, 735, "Dynamic PDF: PDFB Library!");
    }
  }


  // Create a PDF object and set up the properties
  $pdf = new PDF("p", "pt", "letter");
  $pdf->SetAuthor("Universidad Simon Bolivar");
  $pdf->SetTitle("Impresión volante de pago");

  // Add custom font
  $pdf->SetFont("Arial", "", 9);

  // Set line drawing defaults
  $pdf->SetDrawColor(224);
  $pdf->SetLineWidth(1);

  // Load the base PDF into template
  $pdf->setSourceFile("FP2.pdf");
  $tplidx = $pdf->ImportPage(1);

  // Add new page & use the base PDF as template
  $pdf->AddPage();
  $pdf->useTemplate($tplidx,0,0,610,780);
 		 
		
	//primera parte del volante		 
  $y=-5;
  $pdf->SetFont("Arial", "", 12);
  $pdf->Text(500, 25+$y, $nro_ref );
  $pdf->SetFont("Arial", "", 10);
  $pdf->Text(500, 36+$y, $banco );     //a esta altura va el codigo
  $pdf->SetFont("Arial", "", 9);
  $pdf->Text(500, 47+$y, $cuenta );    //a esta altura va el programa
  $pdf->Text(500, 58+$y, $fecha );
  $pdf->Text(500, 78+$y, $identif);
  $pdf->Text(500, 96+$y, $codigo);
  $pdf->Text(100, 75+$y, $periodo);
  $pdf->Text(100, 93+$y, $nombre);
  $pdf->Text(100, 107+$y, $programa);
  $y=187; //observaciones
  $pdf->Text(30, 21+$y, $observacion1);
  $pdf->Text(30, 32+$y, $observacion2);
  $pdf->Text(30, 43+$y, $observacion21);

  $y=139; //conceptos
  if(isset($conceptos) && !empty($conceptos)){
      foreach($conceptos as $pago){
        $pdf->Text(30, $y, $pago['concepto']);
        //$pdf->cell(300, $y, $pago['valor'],0,'R');
        $pdf->SetY($y-8);
            $pdf->Cell(500);
            $pdf->Cell(20,10,$pago['valor'],0,1,'R');
            $y+=10;
        if ($tod_volante!='S') break;
      }
  }
  
  $y=211; //pagos
  if(isset($pagos) && !empty($pagos)){
    foreach($pagos as $pago){
        if($pago['valor']>0){
		$pdf->Text(310, $y, $pago['fecha']);
		$pdf->Text(470, $y, $pago['valor']);
	}
        $y+=12;
        if ($tod_volante!='S') break;
   }
  }
  
  //segunda parte del volante
  $y=252;
  $pdf->SetFont("Arial", "", 12);
  $pdf->Text(500, 25+$y, $nro_ref );
  $pdf->SetFont("Arial", "", 10);
  $pdf->Text(500, 36+$y, $banco );     //a esta altura va el codigo
  $pdf->SetFont("Arial", "", 9);
  $pdf->Text(500, 47+$y, $cuenta );    //a esta altura va el programa
  $pdf->Text(500, 58+$y, $fecha );
  $pdf->Text(500, 74+$y, $identif);
  $pdf->Text(500, 93+$y, $codigo);
  $pdf->Text(100, 75+$y, $periodo);
  $pdf->Text(100, 93+$y, $nombre);
  $pdf->Text(100, 107+$y, $programa);

  $y=458; //observaciones
  $pdf->Text(30, 11+$y, $observacion1);
  $pdf->Text(30, 22+$y, $observacion2);
  $pdf->Text(30, 33+$y, $observacion21);
  
  $y=398;  //conceptos
  
  if(isset($conceptos) && !empty($conceptos)){
    foreach($conceptos as $pago){
    $pdf->Text(30, $y,$pago['concepto']);
    $pdf->SetY($y-8);
	$pdf->Cell(500);
	$pdf->Cell(20,10,$pago['valor'],0,1,'R');
	$y+=10;
        if ($tod_volante!='S') break;
   }
  }
  

  $y=470;  //pagos
  foreach($pagos as $pago){
	   if($pago['valor']>0){
    		$pdf->Text(310, $y, $pago['fecha']);
    		$pdf->Text(470, $y, $pago['valor']);
	   }
    	$y+=12;
        if ($tod_volante!='S') break;
   }
 //tercera parte del volante
  $y=500;
  $x=352;
  $pdf->SetFont("Arial", "", 12);
  $pdf->Text($x-18, 36+$y, $nro_ref );
  $pdf->SetFont("Arial", "", 9);
  $pdf->Text($x+147, 36+$y, $identif );
  $pdf->Text($x+147, 53+$y, $codigo);
  $pdf->Text($x-81, 52+$y, substr($nombre, 0, 37) );
  $pdf->Text($x-73, 68+$y, substr($programa, 0, 37) );
  //$pdf->Text($x-55, 86+$y, $DatosBanco);
	$pdf->SetFont("Arial", "", 10);
	$pdf->Text($x-180, 86+$y,"TAMBIEN PUEDES PAGAR EN: ");
	$pdf->SetFont("Arial", "", 9);
	$pdf->Text($x-30, 86+$y,$ListaBancos);
	

  $y=608;
	//valores y fecha verticales
/* 	foreach($pagos as $pago){ 
		if($pago['valor']>0){
			$fe=substr($pago['fecha'],-10);
			$pdf->Text(80, $y,$fe );
			$pdf->Text(80, $y+16, $pago['valor']);
		}
		$y+=46;
    }
 */
if($cod_valor1>0 && $cod_fecha1<>''){
	$fe=substr($pagos['pago1']['fecha'],-10);
	$pdf->Text(80, $y,$fe );
	$pdf->Text(80, $y+16, $pagos['pago1']['valor']);
}
if($tod_volante=='S')
{
$y+=46;
if($cod_valor2>0 && $cod_fecha2<>''){
	$fe=substr($pagos['pago2']['fecha'],-10);
	$pdf->Text(80, $y,$fe );
	$pdf->Text(80, $y+16, $pagos['pago2']['valor']);
}
$y+=46;
if($cod_valor3>0 && $cod_fecha3<>''){
	$fe=substr($pagos['pago3']['fecha'],-10);
	$pdf->Text(80, $y,$fe );
	$pdf->Text(80, $y+16, $pagos['pago3']['valor']);
}
$y+=46;
if($cod_valor4>0 && $cod_fecha4<>''){
$fe=substr($pagos['pago4']['fecha'],-10);
$pdf->Text(80, $y,$fe );
$pdf->Text(80, $y+16, $pagos['pago4']['valor']);
}
}
 $y+=46;
   /*------------------------------------------------------------------
   * Crea el codigo de barras
  */
  $x=250;
  $y=570;
if($cod_valor1>0 && $cod_fecha1<>''){
	$barcode1    = '415'.$cod_iac;  //Fijo
	$barcode1    .= '8020'.str_pad($nro_ref,12,'0',STR_PAD_LEFT);
	$barcode1    .= '\\F3900'.str_pad($cod_valor1, 8, '0', STR_PAD_LEFT);
	$barcode1    .= '\\F96'.$cod_fecha1;
	$pdf->Image("http://academico.unisimoncucuta.edu.co/volante/barcodes.php?code=UCCEAN128&escapesequences=true&text=".$barcode1."&imageformat=4&dpi=72&rotation=0&modulewidth=0.5&unit=mm",$x,20+$y,283,38,"jpg");
} 


 if($cod_valor2>0 && $cod_fecha2<>'' && $tod_volante=='S'){
	$barcode2    = '415'.$cod_iac;  //Fijo
	$barcode2    .= '8020'.str_pad($nro_ref,12,'0',STR_PAD_LEFT);
	$barcode2    .= '\\F3900'.str_pad($cod_valor2, 8, '0', STR_PAD_LEFT);
	$barcode2    .= '\\F96'.$cod_fecha2;
	$pdf->Image("http://academico.unisimoncucuta.edu.co/volante/barcode.php?code=UCCEAN128&escapesequences=true&text=$barcode2&imageformat=4&dpi=72&rotation=0&modulewidth=0.5&unit=mm",$x,65+$y,283,38,"jpg");
  }
if($cod_valor3>0 && $cod_fecha3<>'' && $tod_volante=='S'){
	$barcode3    = '415'.$cod_iac;  //Fijo
	$barcode3    .= '8020'.str_pad($nro_ref,12,'0',STR_PAD_LEFT);
	$barcode3    .= '\\F3900'.str_pad($cod_valor3, 8, '0', STR_PAD_LEFT);
	$barcode3    .= '\\F96'.$cod_fecha3;
	$pdf->Image("http://academico.unisimoncucuta.edu.co/volante/barcode.php?code=UCCEAN128&escapesequences=true&text=$barcode3&imageformat=4&dpi=72&rotation=0&modulewidth=0.5&unit=mm",$x,110+$y,283,38,"jpg");
 }
if($cod_valor4>0 && $cod_fecha4<>'' && $tod_volante=='S'){
	$barcode4    = '415'.$cod_iac;  //Fijo
	$barcode4    .= '8020'.str_pad($nro_ref,12,'0',STR_PAD_LEFT);
	$barcode4    .= '\\F3900'.str_pad($cod_valor4, 8, '0', STR_PAD_LEFT);
	$barcode4    .= '\\F96'.$cod_fecha4;
	$pdf->Image("http://academico.unisimoncucuta.edu.co/volante/barcode.php?code=UCCEAN128&escapesequences=true&text=$barcode4&imageformat=4&dpi=72&rotation=0&modulewidth=0.5&unit=mm",$x,155+$y,283,38,"jpg");
   }
  $pdf->Output();
  $pdf->closeParsers();


?>