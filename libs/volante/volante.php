<?php

@define('DS', DIRECTORY_SEPARATOR);
@define('ROOTs', realpath(dirname(__FILE__)) . DS);

include_once('db.php');
include_once('NumeroDeVolante.php');


class Volante
{
	var $instancia;
	var $error='';

	function __construct()
	{
		$this->instancia = Db2::getInstance('DBSIANX2');
	}

	public function isPago($num=0)
	{
		$sql = "sELECT B.BCONOMBRE AS BANCOS, P.PAGNUMVOLANTE AS VOLANTE,P.PAGNUMBANCO AS NUMBANCO,P.PAGFECHAPAGOBANCO AS FECHAPAGOBANCO,P.PAGFECHAREGISTSIST AS FECHAREGISTROPAGO,P.PAGESTADO AS ESTADO, P.PAGVALOR AS VALOR FROM DB2ADMIN.PAGOS P inner join DB2ADMIN.BANCOS B on P.PAGNUMBANCO=B.BCOCODIGO WHERE P.PAGFECHAPAGOBANCO >= (CURRENT DATE - 6 MONTHS)  AND P.PAGVALOR>0 AND P.PAGESTADO='A' AND P.PAGNUMVOLANTE=".$num;
		$rta = $this->instancia->getRta($this->instancia->ejecutar($sql));
		if(is_null($rta)){
			$rta = false;
		}else{
			if($rta['ESTADO']!='A'){
				$rta = false;
			}
			if((int)$rta['VALOR']<1){
				$rta = false;
			}
		}
		return $rta;
	}

	public function isPagoExterno($num=0)
	{

		$sql = "sELECT B.BCONOMBRE AS BANCOS,P.PAGNUMVOLANTEEXT AS VOLANTE,P.PAGNUMBANCOEXT AS NUMBANCO,P.PAGFECHAPAGOBANCOEXT AS FECHAPAGOBANCO,P.PAGFECHAREGISTSISTEXT AS FECHAREGISTROPAGO,P.PAGESTADOEXT AS ESTADO, P.PAGVALOREXT AS VALOR FROM DB2ADMIN.PAGOSEXTERNOS P  inner join DB2ADMIN.BANCOS B on P.PAGNUMBANCOEXT=B.BCOCODIGO WHERE P.PAGFECHAPAGOBANCOEXT >= (CURRENT DATE - 6 MONTHS)  AND P.PAGVALOREXT>0 AND P.PAGESTADOEXT='A' AND P.PAGNUMVOLANTEEXT = ".$num." UNION sELECT B.BCONOMBRE AS BANCOS,P.PAGNUMVOLANTE AS VOLANTE,P.PAGNUMBANCO AS NUMBANCO,P.PAGFECHAPAGOBANCO AS FECHAPAGOBANCO,P.PAGFECHAREGISTSIST AS FECHAREGISTROPAGO,P.PAGESTADO AS ESTADO, P.PAGVALOR AS VALOR FROM DB2ADMIN.PAGOS P  inner join DB2ADMIN.BANCOS B on P.PAGNUMBANCO=B.BCOCODIGO inner join DB2ADMIN.VOLANTEEXTERNO PE on PE.VOLEXTNUMERO = P.PAGNUMVOLANTE AND P.PAGFECHAPAGOBANCO >= PE.VOLEXTFECCRE WHERE PE.VOLextFECcre >= (CURRENT DATE - 6 MONTHS)  AND P.PAGVALOR>0  AND P.PAGESTADO='A' AND P.PAGNUMVOLANTE=".$num."";
		$rta = $this->instancia->getRta($this->instancia->ejecutar($sql));
		if(is_null($rta)){
			$rta = false;
		}else{
			if($rta['ESTADO']!='A'){
				$rta = false;
			}
			if((int)$rta['VALOR']<1){
				$rta = false;
			}
		}
		return $rta;
	}

	public function getError()
	{
		return $this->error;
	}

	public function generar($datos=array())
	{
		if (strlen($datos["descripcion"]) > 160){
    	$datos["descripcion"] = substr($datos["descripcion"],0,155).'(...)';
		}

		if(isset($datos['banco']) && $datos['banco']){
			$banco = '51';
			$cuenta = '067000025394';
			$VOLCODTER='51';
		}else{
			$cuenta = '01';
			$banco = '207';
			$VOLCODTER='213';
		}

		$estudiante = $this->getEstudiantes($datos['estudiante']);
//var_dump($estudiante);
        $mtrsemestre = 1;
        $mtrprograma = 911;
		if(isset($datos['programa']))
		  {
		  $mtrprograma =$datos['programa'];
		  }
		  else
		  {
			if( isset($estudiante['MTRPROGRAMA']) ){
				$mtrsemestre = $estudiante['MTRSEMESTRE'];
				if($estudiante['MTRPROGRAMA']<916){
					$mtrprograma = $estudiante['MTRPROGRAMA']+1;
				}
 			}
		  }
		if(isset($datos['semestre']))
		  {
		    $mtrsemestre = $datos['semestre'];
		  }
		
		
		
		if(!isset($datos['tipo'])){
			$datos['tipo'] = 0;
		}

		$periodoActual = $this->getPeriodoactual((int)$datos['tipo']);
	   //var_dump($periodoActual);


		if( !(isset($datos['estudiante'])  && isset($datos['documento'])  && isset($datos['descripcion'])  && isset($datos['fechas'])  ) ){
			$this->error = 'faltan campos obligatorios';
			return false;
		}

		if(!is_array($datos['fechas'])){
			$this->error = 'fecha debe ser array';
			return false;
		}

		if (isset($datos['valores']) && !is_array($datos['valores'])){
			$this->error = 'valores debe ser array';
			return false;
		}

		$fechas = array();
		$f = $datos['fechas'];
		$fechas[1] = $f[0];

		if(isset($f[1])){
			$fechas[2] = $f[1];
		}else{
			$fechas[2] = $f[0];
		}

		if(isset($f[2])){
			$fechas[3] = $f[2];
		}else{
			$fechas[3] = $f[0];
		}

      //var_dump($datos);

		if( !(isset($datos['valores']) && is_array($datos['valores'])  && isset($datos['banco']))   ){

			$valore = $this->valoresdoc($mtrprograma,$datos['documento'],$mtrsemestre,(int)$datos['tipo']);

			$banco = $valore['VLSBANCO'];
			$cuenta = $valore['VLSCUENTA'];

			if($banco=='51'){
				$VOLCODTER='51';
			}else{
				$VOLCODTER='213';
			}

			$valores = array();
			$valores[1] = $valore['VLSVALOR'];
			$valores[2] = $valore['VLSVALOREX1'];
			$valores[3] = $valore['VLSVALOREX2'];

		}else{

			$valores = array();
			$v = $datos['valores'];
			$valores[1] = $v[0];

			if(isset($f[1])){
				$valores[2] = $v[1];
			}else{
				$valores[2] = 0;
			}

			if(isset($f[2])){
				$valores[3] = $v[2];
			}else{
				$valores[3] = 0;
			}
		}


		if(count($valores)<1 || trim($valores[1])=='' ){
			$this->error = 'no encontro valores';
			return;
		}

 //echo 'xxx';
		$sql = "sELECT * FROM DB2ADMIN.VOLANTES WHERE VOLESTUDIANTE=".$datos['estudiante']." and VOLPROGRAMA=".$mtrprograma." and VOLBANCO=".$banco." and VOLCUENTA='".$cuenta."' and VOLTIPCUENTA=1 and VOLDOCUMENTO=".$datos['documento']." and VOLVALOR=".$valores[1]." and VOLVALOREX1=".$valores[2]." and VOLVALOREX2=".$valores[3]." and VOLFECHA='".$fechas[1]."' and VOLFECHA1='".$fechas[2]."' and VOLFECHA2='".$fechas[3]."' and VOLUSUCRE='WEB-VOL' and VOLDEPENUSO=4 and VOLESTADO='A' and VOLTPO=5 and VOLNOMBRE='".$datos['descripcion']."' and VOLSEM=".$mtrsemestre." and VOLPERANO=".$periodoActual['ANO']." and VOLPERSEM=".$periodoActual['SEM']." and VOLFMAPAG=5 and VOLNOTRANS='0' and VOLCODMAQ=0 and VOLGPOTER=4 and VOLCODTER=".$VOLCODTER." and VOLNUMT=0 and VOLBARRA='0' and VOLBARRA1='0' and VOLBARRA2='0' and VOLBARRA3='0' and VOLVALOREX3=0 and VOLFECHA3='".$fechas[3]."' and VOLIP='0' and VOLUSUMOD='0'";
		$rta = $this->instancia->getRta($this->instancia->ejecutar($sql));
		$rta1 = $this->instancia->getResultArray($this->instancia->ejecutar($sql));

		$sql = "sELECT * FROM DB2ADMIN.DOCUMENTOS WHERE DOCCODIGO =".$datos['documento']."";
		$doc = $this->instancia->getRta($this->instancia->ejecutar($sql));

		if(isset($rta['VOLNUMERO']) && (count($rta1) > $doc['DOCMULTIPLES']) ){
			$numvolante = $rta['VOLNUMERO'];
		}else{

			$ObjNumVolante = new NumeroDeVolante($this->instancia->getLink());
			$numvolante = $ObjNumVolante->GetNumeroVolante();

			$sql="iNSERT INTO DB2ADMIN.VOLANTES(VOLNUMERO,VOLESTUDIANTE,VOLPROGRAMA,VOLBANCO,VOLCUENTA,VOLTIPCUENTA,VOLDOCUMENTO,VOLVALOR,VOLVALOREX1,VOLVALOREX2,VOLFECHA,VOLFECHA1,VOLFECHA2,
								 VOLUSUCRE,VOLFECCRE,VOLDEPENUSO,VOLESTADO,VOLTPO,VOLNOMBRE,VOLSEM,VOLPERANO,VOLPERSEM,VOLFMAPAG,VOLNOTRANS,VOLCODMAQ,VOLGPOTER,VOLCODTER,VOLNUMT,
								 VOLBARRA,VOLBARRA1,VOLBARRA2,VOLBARRA3,VOLVALOREX3,VOLFECHA3,VOLIP,VOLCREAHORA,VOLUSUMOD,VOLFECHAMOD)
	                    VALUES(".$numvolante.",".$datos['estudiante'].",".$mtrprograma.",".$banco.",'".$cuenta."',1,".$datos['documento'].",".$valores[1].",".$valores[2].",".$valores[3].",'".$fechas[1]."','".$fechas[2]."','".$fechas[3]."',".
	                    	"'WEB-VOL','".date('d/m/Y')."',4,'A',5,'".$datos['descripcion']."',".$mtrsemestre.",".$periodoActual['ANO'].",".$periodoActual['SEM'].",5,'0',0,4,".$VOLCODTER
	                    	.",0,'0','0','0','0',0,'".$fechas[3]."','0','".date('Y-m-d H:i:s')."','0','".date('Y-m-d H:i:s')."')";

			$this->instancia->ejecutar($sql);
			$ObjNumVolante->SetAsignarNuevoNumero();

			if(isset($datos['detalles'])){
				foreach ($datos['detalles'] as $key => $detalle) {
					if(isset($detalle['signo']) && isset($detalle['descripcion']) && isset($detalle['valores']) ){
						$sql="iNSERT INTO DB2ADMIN.DETALLE_VOLANTES (DTVVOLNUMERO,DTVITEM,DTVTIPDOCUMENTO,DTVSIGNO,DTVPORCENTAJE,DTVVALOR,DTVDESCRIPCION,DTVNUMDOCUMENTO,DTVVALOR1,DTVVALOR2,DTVVALOR3) VALUES ";
						$valor3 = $valor2 = $valor1 = $detalle['valores'][0];
						if( isset($detalle['valores'][1]) ){
							$valor3 = $valor2 = $detalle['valores'][1];
						}
						if( isset($detalle['valores'][2]) ){
							$valor3 = $detalle['valores'][2];
						}

						$sql.="(".$numvolante.",".$key.",0,'".$detalle['signo']."',0,".$valor1.",'".$detalle['descripcion']."',0,".$valor2.",".$valor3.",".$valor3.")";
						$this->instancia->ejecutar($sql);
					}
				}
			}

		}



		if(isset($datos['imprimir']) && $datos['imprimir']){
			$this->imprimir($numvolante,true);
		}

		return $numvolante ;
	}

	private function getPeriodoactual($tipo = 0)
	{
		$sql = '';
		if($tipo==0){//0 academico
			$sql = "sELECT * FROM RELACION.VPERIODOACADEMICO";
		}else if($tipo==1){// 1 web
			$sql = "sELECT * FROM RELACION.VPERIODOMATRICULA";
		}else if($tipo==2){// 2 VACACIONALES
			//$sql = "sELECT * FROM RELACION.VPERIODOVACACIONAL";
		}
		if($sql==''){
			$sql = "sELECT * FROM RELACION.VPERIODOACADEMICO";
		}

		$rta = $this->instancia->getRta($this->instancia->ejecutar($sql));
		return $rta;
	}

	public function valoresdoc($p='',$d='',$s='',$t=0)
	{
		$periodoActual = $this->getPeriodoactual($t);
		$sql = "sELECT * FROM DB2ADMIN.VALORES v where v.VLSPROGRAMA=".$p." and v.VLSDOCUMENTO=".$d." and v.VLSSEMESTRE=".$s." and v.VLSPERIODOANO=".$periodoActual['ANO']." and v.VLSPERIODOSEM=".$periodoActual['SEM']." ";
		$rta = $this->instancia->getRta($this->instancia->ejecutar($sql));

		//si no encuestra busque 0 0
		if(!isset($rta) || count($rta)<1 ){
			$p=0;$s=0;
			$sql = "sELECT * FROM DB2ADMIN.VALORES v where v.VLSPROGRAMA=".$p." and v.VLSDOCUMENTO=".$d." and v.VLSSEMESTRE=".$s." and v.VLSPERIODOANO=".$periodoActual['ANO']." and v.VLSPERIODOSEM=".$periodoActual['SEM']." ";
			$rta = $this->instancia->getRta($this->instancia->ejecutar($sql));
		}

		return $rta;
	}

	public function fechavolante( $d='' )
	{
		$periodoActual = $this->getPeriodoactual(0);
		$sql = "sELECT * FROM DB2ADMIN.PARAMVOLANTES v where v.PRMESTADO='A'  and v.PRMDOCTO=".$d." and v.PRMSEM=".$periodoActual['SEM']." ";
		$rta = $this->instancia->getRta($this->instancia->ejecutar($sql));
		return $rta;
	}


	private function getEstudiantes($cod='')
	{

		$sql ="Select m.mtrprograma, m.mtrsemestre from db2admin.matriculas m inner join db2admin.programas p on m.mtrprograma = p.prgcodprog
		and   m.mtrcodigoest = ".$cod."
		where
		mtrperiodoano*10+mtrperiodosem = (select max(mtrperiodoano*10+mtrperiodosem) from db2admin.matriculas m
		inner join db2admin.programas p on m.mtrprograma = p.prgcodprog
		and  m.mtrcodigoest = ".$cod.")";
		//echo  $sql;
		
		/*		$sql ="Select m.mtrprograma, m.mtrsemestre from db2admin.matriculas m inner join db2admin.programas p on m.mtrprograma = p.prgcodprog
		and (prgtipo = 1 or prgtipo = 2) and m.mtrcodigoest = ".$cod."
		where
		mtrperiodoano*10+mtrperiodosem = (select max(mtrperiodoano*10+mtrperiodosem) from db2admin.matriculas m
		inner join db2admin.programas p on m.mtrprograma = p.prgcodprog
		and (prgtipo = 1 or prgtipo = 2) and m.mtrcodigoest = ".$cod.")";*/
		
		
		/*$sql = "sELECT *
				FROM
				DB2ADMIN.MATRICULAS M INNER JOIN
				(SELECT max(M.MTRPERIODOSEM) AS SEM
				FROM
				DB2ADMIN.MATRICULAS M
				INNER JOIN
				(SELECT max(M.MTRPERIODOANO) AS ANO
				FROM
				DB2ADMIN.MATRICULAS M
				WHERE
				M.MTRESTADO='A' AND M.MTRCODIGOEST= ".$cod.") A ON A.ANO=M.MTRPERIODOANO
				WHERE
				M.MTRESTADO='A' AND M.MTRCODIGOEST= ".$cod.") S ON S.SEM=M.MTRPERIODOSEM
				INNER JOIN
				(SELECT
				max(M.MTRPERIODOANO) AS ANOS
				FROM
				DB2ADMIN.MATRICULAS M
				WHERE M.MTRESTADO='A' AND M.MTRCODIGOEST= ".$cod.") A ON A.ANOS=M.MTRPERIODOANO
				WHERE M.MTRCODIGOEST= ".$cod."";*/

		$rta = $this->instancia->getRta($this->instancia->ejecutar($sql));
		return $rta;
	}

	public function imprimir($id='', $download=false)
	{

		if($this->isPago($id)){
			print "<script>alert('Este volante ya fue pagado, por este motivo no se puede generar este volante de pago, informar a dsrt@unisimonbolivar.edu.co');</script>";
			exit;
		}

		$now = strtotime(date('Y-m-d'));

		include_once('fpdf'.DS.'fpdf.php');
		include_once('fpdf'.DS.'fpdi'.DS.'fpdi.php');

		$pdf = new FPDI("p", "pt", "letter");
		$pdf->SetAuthor("Inprosistemas del Norte");
		$pdf->SetTitle("Impresión volante de pago");



		$pageCount = $pdf->setSourceFile(ROOTs.'plantilla'.DS.'FP3.pdf');
		$tplidx = $pdf->ImportPage(1);

		$pdf->AddPage();
		$pdf->useTemplate($tplidx,0,0,610,780);

		$volante = $id;
		$num_volante=$volante;
		$volante = $this->getVolante($volante);

		if(!$volante){
			$this->error = 'no encontro volante con id ('.$id.')';
			return;
		}

		$campo = $volante;

		$tod_volante='S';
		if(isset($this->getTexto['todos'])){
			$tod_volante=$this->getTexto['todos'];
		}



		/*+++++++++*/

		$valor       = $campo['VOLVALOR'];
		$periodo_year= $campo['VOLPERANO'];
		$periodo_sem= $campo['VOLPERSEM'];
		$nro_ref     = $num_volante;
		$banco    = $campo['BCONOMBRE'];
		$cuenta   = $campo['VOLCUENTA'];
		$fecha    = date('d/m/Y',strtotime($campo['VOLFECCRE']));
		$identif  = $campo['ESTIDE'];
		$periodo  = $periodo_year.'-'.$periodo_sem;
		$nombre   = $campo['ESTAPELLIDO1'].' '.$campo['ESTAPELLIDO2'].' '.$campo['ESTNOMBRE1'].' '.$campo['ESTNOMBRE2'];
		$codigo   = $campo['VOLESTUDIANTE'];
		$programa = $campo['PRGNOMBRE'];


            //fecha y valor para cod barra
		$QuitarFecha1=$QuitarFecha2=$QuitarFecha3=$QuitarFecha4=true;
		$cod_valor4=$cod_fecha4=$cod_fecha3=$cod_fecha2=$cod_fecha1='';

		if($campo['VOLFECHA']<>'0001-01-01'){
			if($campo['VOLFECHA']<>'1900-01-01'){
				if(strtotime($campo['VOLFECHA']) >= $now){
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
				if(strtotime($campo['VOLFECHA1']) >= $now){
					$f=explode('-',$campo['VOLFECHA1']);
					$ano=$f[0];
					$mes=$f[1];
					$dia=$f[2];
					$cod_fecha2=$ano.$mes.$dia;
					$QuitarFecha2=false;

					if($cod_fecha1==$cod_fecha2){
						$cod_fecha2 ='';
					}
				}else{
					$QuitarFecha2=true;
				}
			}
		}
		if($campo['VOLFECHA2']<>'0001-01-01'){
			if($campo['VOLFECHA2']<>'1900-01-01'){
				if(strtotime($campo['VOLFECHA2']) >= $now){
					$f=explode('-',$campo['VOLFECHA2']);
					$ano=$f[0];
					$mes=$f[1];
					$dia=$f[2];
					$cod_fecha3=$ano.$mes.$dia;
					$QuitarFecha3=false;
					if($cod_fecha1==$cod_fecha3){
						$cod_fecha3 ='';
					}
					if($cod_fecha2==$cod_fecha3){
						$cod_fecha3 ='';
					}
				}else{
					$QuitarFecha3=true;
				}
			}
		}
		/*if($campo['VOLFECHA3']<>'0001-01-01'){
			if($campo['VOLFECHA3']<>'1900-01-01'){
				if(strtotime($campo['VOLFECHA3']) >= $now){
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
		}*/
		$cod_fecha4='';
		$QuitarFecha4=true;
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
		$ListaBancos = $this->listabancos($cod_iac);





		if($cod_fecha1<>''){
			$pago['fecha']='Ordinaria                '.date('d/m/Y',strtotime($campo['VOLFECHA']));
			$pago['valor']=number_format($campo["VOLVALOR"],0,".",",");
			$pagos['pago1']=$pago;
		}
		if($cod_fecha2<>''){
			$pago['fecha']='Extra Ordinaria I     '.date('d/m/Y',strtotime($campo['VOLFECHA1']));
			$pago['valor']=number_format($campo['VOLVALOREX1'],0,".",",");
			$pagos['pago2']=$pago;
		}
		if($cod_fecha3<>''){
			$pago['fecha']='Extra Ordinaria II    '.date('d/m/Y',strtotime($campo['VOLFECHA2']));
			$pago['valor']=number_format($campo['VOLVALOREX2'],0,".",",");
			$pagos['pago3']=$pago;
		}
		if($cod_fecha4<>''){
			$pago['fecha']='Extra Ordinaria III   '.date('d/m/Y',strtotime($campo['VOLFECHA3']));
			$pago['valor']=number_format($campo['VOLVALOREX3'],0,".",",");
			$pagos['pago4']=$pago;
		}


		$rta = $this->volanteDetalle($nro_ref);
		$conceptos = array();

		foreach ($rta as $key => $value) {


			$rta2 = explode('|',$value['DTVDESCRIPCION'] );
			if(count($rta2)>0){
				$temp = true;
				foreach ($rta2 as $key => $values) {
					$p['concepto']='* '.$values;
					if($temp){
						$p['valor']='$ '.number_format($value['DTVVALOR'],0,".",",");
						$temp = false;
					}else{
						$p['valor']='';
					}
					$conceptos[]=$p;
				}
			}else{
				$pago['concepto']=$value['DTVSIGNO'].'  '.$value['DTVDESCRIPCION'];
				$pago['valor']='$ '.number_format($value['DTVVALOR'],0,".",",");
				$conceptos[]=$pago;
			}



		}


		if(count($conceptos)<1){
			$p = array();
			$p['concepto']=$campo['VOLDOCUMENTO'].' - '.$campo['DOCNOMBRE'];
			$p['valor']='$ '.number_format($campo["VOLVALOR"],0,".",",");
			$conceptos[]=$p;

			//$p['concepto']='* '.$campo['VOLNOMBRE'];
			/*$rta = explode('|',$campo['VOLDOCUMENTO'] );
			foreach ($rta as $key => $value) {
				$p['concepto']='* '.$value;
				$p['valor']='';
				$conceptos[]=$p;
			}*/

			$rta = explode('|',$campo['VOLNOMBRE'] );
			foreach ($rta as $key => $value) {
				$p['concepto']='* '.$value;
				$p['valor']='';
				$conceptos[]=$p;
			}


		}





		/*+++++++++*/







		$observacion1='1. Para pagar este Volante tener en cuenta el Total a Pagar.';
		$observacion2='2. Estudiante por favor verifique su estado academico';
		$observacion21='  para saber el Semestre a matricular.';
		$DatosBanco="BANCO: ".$banco.' CUENTA N° '.$cuenta;




		$ob = $this->volanteOb($campo['DOCCODIGO']);
		$ob = explode('|', @$ob[0]);
		//var_dump($ob[0]);




















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
  $y=190; //observaciones

  	$y+=10; //aqui
  	$pdf->SetY($y);
  foreach ($ob as $key => $value) {
  	$pdf->MultiCell(260, 10, $value,0,'J');
  	$pdf->ln(0);
  	//$pdf->Text(30, $y, $value);

  }


  /*$pdf->Text(30, 21+$y, $observacion1);
  $pdf->Text(30, 32+$y, $observacion2);
  $pdf->Text(30, 43+$y, $observacion21);*/

  $y=131; //conceptos
  if(isset($conceptos) && !empty($conceptos)){
  	foreach($conceptos as $pago){
  		$pdf->SetY($y);
  		$pdf->MultiCell(415, 10, $pago['concepto'],0);
  		//$pdf->Text(30, $y,$pago['concepto']);
  		//$pdf->SetY($y-8);
  		//$pdf->Cell(500);
  		$pdf->SetY($y);
  		$pdf->Cell(430);
  		$pdf->Cell(110,10,$pago['valor'],0,1,'R');
  		$y+=10;
  		if ($tod_volante!='S') break;
  	}
  }

  $y=211; //pagos
  if(isset($pagos) && !empty($pagos)){
  	foreach($pagos as $pago){
  		if($pago['valor']>0){
  			$pdf->Text(310, $y, $pago['fecha']);
  			$pdf->Text(470, $y, '$ '.$pago['valor']);
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

  $y=444; //observaciones

  	$y+=10; //aqui
  	$pdf->SetY($y);
  foreach ($ob as $key => $value) {
  	$pdf->MultiCell(260, 10, $value,0,'J');
  	$pdf->ln(0);
  	//$pdf->Text(30, $y, $value);

  }
  /*$pdf->Text(30, 11+$y, $observacion1);
  $pdf->Text(30, 22+$y, $observacion2);
  $pdf->Text(30, 33+$y, $observacion21);*/

  $y=390;  //conceptos

  if(isset($conceptos) && !empty($conceptos)){
  	foreach($conceptos as $pago){
  		$pdf->SetY($y);
  		$pdf->MultiCell(415, 10, $pago['concepto'],0);
  		//$pdf->Text(30, $y,$pago['concepto']);
  		//$pdf->SetY($y-8);
  		//$pdf->Cell(500);
  		$pdf->SetY($y);
  		$pdf->Cell(430);
  		$pdf->Cell(110,10,$pago['valor'],0,1,'R');
  		$y+=10;
  		if ($tod_volante!='S') break;
  	}
  }


  $y=470;  //pagos
  foreach($pagos as $pago){
  	if($pago['valor']>0){
  		$pdf->Text(310, $y, $pago['fecha']);
  		$pdf->Text(470, $y, '$ '.$pago['valor']);
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

  if($cod_valor1>0 && $cod_fecha1<>''){
  	$fe=substr($pagos['pago1']['fecha'],-10);
  	$pdf->Text(80, $y,$fe );
  	$pdf->Text(80, $y+16, '$ '.$pagos['pago1']['valor']);
  }
  if($tod_volante=='S')
  {
  	$y+=46;
  	if($cod_valor2>0 && $cod_fecha2<>''){
  		$fe=substr($pagos['pago2']['fecha'],-10);
  		$pdf->Text(80, $y,$fe );
  		$pdf->Text(80, $y+16, '$ '.$pagos['pago2']['valor']);
  	}
  	$y+=46;
  	if($cod_valor3>0 && $cod_fecha3<>''){
  		$fe=substr($pagos['pago3']['fecha'],-10);
  		$pdf->Text(80, $y,$fe );
  		$pdf->Text(80, $y+16, '$ '.$pagos['pago3']['valor']);
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
  $pdf->Image("http://academico.unisimoncucuta.edu.co/barra/barcodes.php?code=UCCEAN128&escapesequences=true&text=".$barcode1."&imageformat=4&dpi=72&rotation=0&modulewidth=0.5&unit=mm",$x,20+$y,283,38,"jpg");
}


if($cod_valor2>0 && $cod_fecha2<>'' && $tod_volante=='S'){
  $barcode2    = '415'.$cod_iac;  //Fijo
  $barcode2    .= '8020'.str_pad($nro_ref,12,'0',STR_PAD_LEFT);
  $barcode2    .= '\\F3900'.str_pad($cod_valor2, 8, '0', STR_PAD_LEFT);
  $barcode2    .= '\\F96'.$cod_fecha2;
  $pdf->Image("http://academico.unisimoncucuta.edu.co/barra/barcodes.php?code=UCCEAN128&escapesequences=true&text=$barcode2&imageformat=4&dpi=72&rotation=0&modulewidth=0.5&unit=mm",$x,65+$y,283,38,"jpg");
}
if($cod_valor3>0 && $cod_fecha3<>'' && $tod_volante=='S'){
  $barcode3    = '415'.$cod_iac;  //Fijo
  $barcode3    .= '8020'.str_pad($nro_ref,12,'0',STR_PAD_LEFT);
  $barcode3    .= '\\F3900'.str_pad($cod_valor3, 8, '0', STR_PAD_LEFT);
  $barcode3    .= '\\F96'.$cod_fecha3;
  $pdf->Image("http://academico.unisimoncucuta.edu.co/barra/barcodes.php?code=UCCEAN128&escapesequences=true&text=$barcode3&imageformat=4&dpi=72&rotation=0&modulewidth=0.5&unit=mm",$x,110+$y,283,38,"jpg");
}
if($cod_valor4>0 && $cod_fecha4<>'' && $tod_volante=='S'){
  $barcode4    = '415'.$cod_iac;  //Fijo
  $barcode4    .= '8020'.str_pad($nro_ref,12,'0',STR_PAD_LEFT);
  $barcode4    .= '\\F3900'.str_pad($cod_valor4, 8, '0', STR_PAD_LEFT);
  $barcode4    .= '\\F96'.$cod_fecha4;
  $pdf->Image("http://academico.unisimoncucuta.edu.co/barra/barcodes.php?code=UCCEAN128&escapesequences=true&text=$barcode4&imageformat=4&dpi=72&rotation=0&modulewidth=0.5&unit=mm",$x,155+$y,283,38,"jpg");
}

if($download == 1){
	$this->deletePdf();
	$pdf->Output(ROOTs.'files'.DS.'Volante_'.$id.'.pdf');
	$this->output_file(ROOTs.'files'.DS.'Volante_'.$id.'.pdf','Volante_'.$id.'.pdf','application/pdf');
}else if($download == 2){
	$this->deletePdf();
	$pdf->Output(ROOTs.'files'.DS.'Volante_'.$id.'.pdf');
	return $id;
}else{
	$pdf->Output();
}




}
public function deletePdf()
{
	$dir = ROOTs.'files'.DS;
	$ach = scandir($dir);
	$cnt = count($ach);
	$now = strtotime(date('Y-m-d'));
	for($i=0;$i<$cnt;$i++){
		if($ach[$i]!='.' and $ach[$i]!='..'){
			if($now>strtotime(date('y-m-d',filectime($dir.$ach[$i])))){
				unlink($dir.$ach[$i]);
			}
		}
	}
}

private function output_file($file, $name, $mime_type='')
{
 /*
 This function takes a path to a file to output ($file),
 the filename that the browser will see ($name) and
 the MIME type of the file ($mime_type, optional).

 If you want to do something on download abort/finish,
 register_shutdown_function('function_name');
 */
 if(!is_readable($file)) die('File not found or inaccessible!');

 $size = filesize($file);
 $name = rawurldecode($name);

 /* Figure out the MIME type (if not specified) */
 $known_mime_types=array(
 	"pdf" => "application/pdf",
 	"txt" => "text/plain",
 	"html" => "text/html",
 	"htm" => "text/html",
 	"exe" => "application/octet-stream",
 	"zip" => "application/zip",
 	"doc" => "application/msword",
 	"xls" => "application/vnd.ms-excel",
 	"ppt" => "application/vnd.ms-powerpoint",
 	"gif" => "image/gif",
 	"png" => "image/png",
 	"jpeg"=> "image/jpg",
 	"jpg" =>  "image/jpg",
 	"php" => "text/plain"
 	);

 if($mime_type==''){
 	$file_extension = strtolower(substr(strrchr($file,"."),1));
 	if(array_key_exists($file_extension, $known_mime_types)){
 		$mime_type=$known_mime_types[$file_extension];
 	} else {
 		$mime_type="application/force-download";
 	};
 };

 @ob_end_clean(); //turn off output buffering to decrease cpu usage

 // required for IE, otherwise Content-Disposition may be ignored
 if(ini_get('zlib.output_compression'))
 	ini_set('zlib.output_compression', 'Off');

 header('Content-Type: ' . $mime_type);
 header('Content-Disposition: attachment; filename="'.$name.'"');
 header("Content-Transfer-Encoding: binary");
 header('Accept-Ranges: bytes');

 /* The three lines below basically make the
 download non-cacheable */
 header("Cache-control: private");
 header('Pragma: private');
 header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

 // multipart-download and download resuming support
 if(isset($_SERVER['HTTP_RANGE']))
 {
 	list($a, $range) = explode("=",$_SERVER['HTTP_RANGE'],2);
 	list($range) = explode(",",$range,2);
 	list($range, $range_end) = explode("-", $range);
 	$range=intval($range);
 	if(!$range_end) {
 		$range_end=$size-1;
 	} else {
 		$range_end=intval($range_end);
 	}

 	$new_length = $range_end-$range+1;
 	header("HTTP/1.1 206 Partial Content");
 	header("Content-Length: $new_length");
 	header("Content-Range: bytes $range-$range_end/$size");
 } else {
 	$new_length=$size;
 	header("Content-Length: ".$size);
 }

 /* output the file itself */
 $chunksize = 1*(1024*1024); //you may want to change this
 $bytes_send = 0;
 if ($file = fopen($file, 'r'))
 {
 	if(isset($_SERVER['HTTP_RANGE']))
 		fseek($file, $range);

 	while(!feof($file) &&
 		(!connection_aborted()) &&
 		($bytes_send<$new_length)
 		)
 	{
 		$buffer = fread($file, $chunksize);
        print($buffer); //echo($buffer); // is also possible
        flush();
        $bytes_send += strlen($buffer);
    }
    fclose($file);
} else die('Error - can not open file.');

die();
}


private function volanteDetalle($id){

	$sql="sELECT DTVNUMDOCUMENTO, DTVVALOR,DTVDESCRIPCION,DTVSIGNO
	FROM DB2ADMIN.DETALLE_VOLANTES
	WHERE DB2ADMIN.DETALLE_VOLANTES.DTVVOLNUMERO=".$id."
	ORDER BY DB2ADMIN.DETALLE_VOLANTES.DTVITEM";



	$rta = $this->instancia->getResultArray($this->instancia->ejecutar($sql));

	return $rta;



}
private function listabancos($CodIAC=''){
	$sql="Select BCORESUMEN from DB2ADMIN.bancos, DB2ADMIN.cuentas where bcoCodigo = ctaBanco and CtaCodIac ='".$CodIAC."' order By BcoOrden";
	$rta = $this->instancia->getResultArray($this->instancia->ejecutar($sql));
	$ListaBancos='';
	foreach ($rta as $key => $value) {
		$ListaBancos.=$value['BCORESUMEN'].' | ';
	}
	return $ListaBancos;
}
 function listarvolantes($criterios){
 	 	 $rta = $this->instancia->getResultArray($this->instancia->ejecutar($criterios));
 	     return $rta;
 }

private function volanteOb($id=''){


	$sql="select * from RELACION.VOLANTEOB v where v.documento=".$id;
	$rta = $this->instancia->getResultArray($this->instancia->ejecutar($sql));

	if($rta && isset($rta[0])){
		$rta = $rta[0];
		$rta = explode('<br/>', $rta['OBSERVACION']);
	}else{
		$rta = array();
	}

	return $rta;
}

private function getVolante($num_volante)
{



	$sql="sELECT
	DB2ADMIN.VOLANTES.VOLESTUDIANTE,
	DB2ADMIN.VOLANTES.VOLPERANO,
	DB2ADMIN.VOLANTES.VOLPERSEM,
	DB2ADMIN.VOLANTES.VOLNUMERO,
	DB2ADMIN.VOLANTES.VOLFECCRE,
	DB2ADMIN.ESTUDIANTES.ESTIDE,
	DB2ADMIN.VOLANTES.VOLCUENTA,
	DB2ADMIN.BANCOS.BCONOMBRE,
	DB2ADMIN.BANCOS.BCOCODIGO,
	DB2ADMIN.VOLANTES.VOLPROGRAMA,
	DB2ADMIN.PROGRAMAS.PRGNOMBRE,
	DB2ADMIN.DOCUMENTOS.DOCNOMBRE,
	DB2ADMIN.DOCUMENTOS.DOCCODIGO,
	DB2ADMIN.VOLANTES.VOLVALOR,
	DB2ADMIN.VOLANTES.VOLVALOREX1,
	DB2ADMIN.VOLANTES.VOLVALOREX2,
	DB2ADMIN.VOLANTES.VOLVALOREX3,
	DB2ADMIN.VOLANTES.VOLFECHA,
	DB2ADMIN.VOLANTES.VOLNOMBRE,
	DB2ADMIN.VOLANTES.VOLFECHA1,
	DB2ADMIN.VOLANTES.VOLFECHA2,
	DB2ADMIN.VOLANTES.VOLFECHA3,
	DB2ADMIN.VOLANTES.VOLDEPENUSO,
	DB2ADMIN.VOLANTES.VOLDOCUMENTO,
	DB2ADMIN.ESTUDIANTES.ESTAPELLIDO1,
	DB2ADMIN.ESTUDIANTES.ESTAPELLIDO2,
	DB2ADMIN.ESTUDIANTES.ESTNOMBRE1,
	DB2ADMIN.ESTUDIANTES.ESTNOMBRE2,
	DB2ADMIN.CUENTAS.CTACODIAC
	FROM
	(DB2ADMIN.BANCOS INNER JOIN (((DB2ADMIN.VOLANTES INNER JOIN DB2ADMIN.DOCUMENTOS ON DB2ADMIN.VOLANTES.VOLDOCUMENTO = DB2ADMIN.DOCUMENTOS.DOCCODIGO)
		INNER JOIN DB2ADMIN.PROGRAMAS ON DB2ADMIN.VOLANTES.VOLPROGRAMA = DB2ADMIN.PROGRAMAS.PRGCODPROG)
INNER JOIN DB2ADMIN.ESTUDIANTES ON DB2ADMIN.VOLANTES.VOLESTUDIANTE = DB2ADMIN.ESTUDIANTES.ESTCODIGO) ON DB2ADMIN.BANCOS.BCOCODIGO = DB2ADMIN.VOLANTES.VOLBANCO)
INNER JOIN DB2ADMIN.CUENTAS ON (DB2ADMIN.VOLANTES.VOLBANCO = DB2ADMIN.CUENTAS.CTABANCO)
AND (DB2ADMIN.VOLANTES.VOLTIPCUENTA = DB2ADMIN.CUENTAS.CTATIPCUENTA)
AND (DB2ADMIN.VOLANTES.VOLCUENTA = DB2ADMIN.CUENTAS.CTACUENTA)
WHERE
DB2ADMIN.VOLANTES.VOLNUMERO=".$num_volante."";



$rta = $this->instancia->getRta($this->instancia->ejecutar($sql));

return $rta;

}


private function getVolanteE($num_volante)
{



	$sql="sELECT
	*
FROM
DB2ADMIN.BANCOS
INNER JOIN DB2ADMIN.VOLANTEEXTERNO v ON DB2ADMIN.BANCOS.BCOCODIGO = v.VOLEXTBANCO
INNER JOIN DB2ADMIN.DOCUMENTOS ON v.VOLEXTDOCUMENTO = DB2ADMIN.DOCUMENTOS.DOCCODIGO
INNER JOIN DB2ADMIN.EXTERNOS ON v.VOLEXTERNO = DB2ADMIN.EXTERNOS.EXTIDE
INNER JOIN DB2ADMIN.CUENTAS ON (v.VOLEXTBANCO = DB2ADMIN.CUENTAS.CTABANCO) AND (v.VOLEXTTIPCUENTA = DB2ADMIN.CUENTAS.CTATIPCUENTA) AND (v.VOLEXTCUENTA = DB2ADMIN.CUENTAS.CTACUENTA)
WHERE v.VOLEXTNUMERO=".$num_volante."";


$rta = $this->instancia->getRta($this->instancia->ejecutar($sql));

return $rta;

}

/*++++++++++++++++++++++++++++++++++++++*/
public function generarExterno($datos=array())
{
	if (strlen($datos["descripcion"]) > 160){
    	$datos["descripcion"] = substr($datos["descripcion"],0,155).'(...)';
	}


	if(isset($datos['banco']) && $datos['banco']){
		$banco = '51';
		$cuenta = '067000024199';
		$VOLCODTER = '51';
	}else{
		$cuenta = '01';
		$banco = '207';
		$VOLCODTER = '213';
	}

	$valores = array();
	$v = $datos['valores'];
		$valores[1] = $v[0];

	if(isset($f[1])){
		$valores[2] = $v[1];
	}else{
		$valores[2] = $v[0];
	}
	if(isset($f[2])){
		$valores[3] = $v[2];
	}else{
		$valores[3] = $v[0];
	}

	$fechas = array();
	$f = $datos['fechas'];
	$fechas[1] = $f[0];

	if(isset($f[1])){
		$fechas[2] = $f[1];
	}else{
		$fechas[2] = $f[0];
	}
	if(isset($f[2])){
		$fechas[3] = $f[2];
	}else{
		$fechas[3] = $f[0];
	}

	$periodoActual = $this->getPeriodoactual((int)$datos['tipo']);

	$p = $this->getExterno($datos['persona']['documento']);
	if(!$p){
		$rta = $this->insertExterno($datos['persona']);
		if(!$rta){
			return false;
		}
		$p = $this->getExterno($datos['persona']['documento']);
	}

	$sql = "sELECT * FROM DB2ADMIN.VOLANTEEXTERNO WHERE VOLEXTERNO=".$datos['persona']['documento']." and VOLEXTPROGRAMA=0 and VOLEXTBANCO=".$banco." and VOLEXTCUENTA='".$cuenta."' and VOLEXTTIPCUENTA=1 and VOLEXTDOCUMENTO=".$datos['documento']." and VOLEXTVALOR=".$valores[1]." and VOLEXTVALOREX1=".$valores[2]." and VOLEXTVALOREX2=".$valores[3]." and VOLEXTFECHA='".$fechas[1]."' and VOLEXTFECHA1='".$fechas[2]."' and VOLEXTFECHA2='".$fechas[3]."' and VOLEXTUSUCRE='WEB-VOL' and VOLEXTDEPENUSO=4 and VOLEXTESTADO='A' and VOLEXTTPO=5 and VOLEXTDESCRIPCION='".$datos['descripcion']."' and VOLEXTSEM=0 and VOLEXTPERANO=".$periodoActual['ANO']." and VOLEXTPERSEM=".$periodoActual['SEM']."";
	$rta = $this->instancia->getRta($this->instancia->ejecutar($sql));

	if(isset($rta['VOLEXTNUMERO'])){
		$numvolante = $rta['VOLEXTNUMERO'];
	}else{

		$ObjNumVolante = new NumeroDeVolante($this->instancia->getLink(),8);
		$numvolante = $ObjNumVolante->GetNumeroVolante();


		$sql = "iNSERT INTO DB2ADMIN.VOLANTEEXTERNO (VOLEXTNUMERO,VOLEXTERNO,VOLEXTPROGRAMA,VOLEXTBANCO,VOLEXTCUENTA,VOLEXTTIPCUENTA,VOLEXTDOCUMENTO,VOLEXTVALOR,VOLEXTVALOREX1,VOLEXTVALOREX2,VOLEXTFECHA,VOLEXTFECHA1,VOLEXTFECHA2,VOLEXTUSUCRE,VOLEXTFECCRE,VOLEXTDEPENUSO,VOLEXTESTADO,VOLEXTTPO,VOLEXTDESCRIPCION,VOLEXTSEM,VOLEXTPERANO,VOLEXTPERSEM)
				VALUES (".$numvolante.",".$datos['persona']['documento'].",0,".$banco.",'".$cuenta."',1,".$datos['documento'].",".$valores[1].",".$valores[2].",".$valores[3].",'".$fechas[1]."','".$fechas[2]."','".$fechas[3]."','WEB-VOL','".date('d/m/Y')."',4,'A',5,'".$datos['descripcion']."',0,".$periodoActual['ANO'].",".$periodoActual['SEM'].")";

		$this->instancia->ejecutar($sql);
		$ObjNumVolante->SetAsignarNuevoNumero();
	}



	if(isset($datos['imprimir']) && $datos['imprimir']){
		$this->imprimirExterno($numvolante,true);
	}
	return $numvolante ;
}

private function getExterno($id='0')
{
	$sql="select * from DB2ADMIN.EXTERNOS e where e.EXTIDE=".$id;
	$rta = $this->instancia->getRta($this->instancia->ejecutar($sql));
	return $rta;
}
private function insertExterno($p = array())
{

	if( !(isset($p['nombres']) && isset($p['apellidos']) && isset($p['documento']) && isset($p['tipodocumento']) && isset($p['expedida']))){
		$this->error = 'faltan campos obligatorios';
		return false;
	}
	if(!isset($p['direccion'])){
		$p['direccion'] = '';
	}
	if(!isset($p['telefono'])){
		$p['telefono'] = '';
	}
	if(!isset($p['correo'])){
		$p['correo'] = '';
	}

	$p['nombres'] = mb_strtoupper($p['nombres'], "utf-8");
	$nombres = explode(' ', $p['nombres']);
	if(!isset($nombres[1])){
		$nombres[1] = '';
	}

	$p['apellidos'] = mb_strtoupper($p['apellidos'], "utf-8");
	$apellidos = explode(' ', $p['apellidos']);
	if(!isset($apellidos[1])){
		$apellidos[1] = '';
	}

	$sql = "iNSERT INTO DB2ADMIN.EXTERNOS (EXTIDE,EXTTIPO,EXTTIPOIDE,EXTEXPEDIDA,EXTNOMBRE1,EXTNOMBRE2,EXTAPELLIDO1,EXTAPELLIDO2,EXTRAZON,EXTDIRECCION,EXTTELEFONO,EXTCORREO,EXTUSUCRE,EXTFECCRE,ESTESTADO)
			VALUES (".$p['documento'].",1,'".$p['tipodocumento']."','".substr($p['expedida'],0,29)."','".$nombres[0]."','".$nombres[1]."','".$apellidos[0]."','".$apellidos[1]."','".$p['nombres'].' '.$p['apellidos']."','".$p['direccion']."','".$p['telefono']."','".$p['correo']."','WEB-VOL','".date('d/m/Y')."','A')";
	$this->instancia->ejecutar($sql);

	return true;

}

public function imprimirExterno($id='', $download=false)
	{

		if($this->isPagoExterno($id)){
			print "<script>alert('Este volante ya fue pagado, por este motivo no se puede generar este volante de pago, informar a dsrt@unisimonbolivar.edu.co');</script>";
			exit;
		}

		$now = strtotime(date('Y-m-d'));
		include_once('fpdf'.DS.'fpdf.php');
		include_once('fpdf'.DS.'fpdi'.DS.'fpdi.php');

		$pdf = new FPDI("p", "pt", "letter");
		$pdf->SetAuthor("Universidad Simon Bolivar");
		$pdf->SetTitle("Impresión volante de pago");



		$pageCount = $pdf->setSourceFile(ROOTs.'plantilla'.DS.'FP2.pdf');
		$tplidx = $pdf->ImportPage(1);

		$pdf->AddPage();
		$pdf->useTemplate($tplidx,0,0,610,780);

		$volante = $id;
		$num_volante=$volante;
		$volante = $this->getVolanteE($volante);

		if(!$volante){
			$this->error = 'no encontro volante con id ('.$id.')';
			return;
		}

		$campo = $volante;

		$tod_volante='S';
		if(isset($this->getTexto['todos'])){
			$tod_volante=$this->getTexto['todos'];
		}



		/*+++++++++*/



		$valor       = $campo['VOLEXTVALOR'];
		$periodo_year= $campo['VOLEXTPERANO'];
		$periodo_sem= $campo['VOLEXTPERSEM'];
		$nro_ref     = $num_volante;
		$banco    = $campo['BCONOMBRE'];
		$cuenta   = $campo['VOLEXTCUENTA'];
		$fecha    = date('d/m/Y',strtotime($campo['VOLEXTFECCRE']));
		$identif  = $campo['VOLEXTERNO'];
		$periodo  = $periodo_year.'-'.$periodo_sem;
		$nombre   = strtoupper($campo['EXTAPELLIDO1'].' '.$campo['EXTAPELLIDO2'].' '.$campo['EXTNOMBRE1'].' '.$campo['EXTNOMBRE2']);
		$codigo   = 'N/A';
		$programa = 'N/A';




            //fecha y valor para cod barra
		$QuitarFecha1=$QuitarFecha2=$QuitarFecha3=true;
		$cod_valor4=$cod_fecha4=$cod_fecha3=$cod_fecha2=$cod_fecha1='';

		if($campo['VOLEXTFECHA']<>'0001-01-01'){
			if($campo['VOLEXTFECHA']<>'1900-01-01'){
				if(strtotime($campo['VOLEXTFECHA']) >= $now){
					$f=explode('-',$campo['VOLEXTFECHA']);
					$ano=$f[0];
					$mes=$f[1];
					$dia=$f[2];
					$cod_fecha1=$ano.$mes.$dia;
					$QuitarFecha1=false;
				}else{
					$QuitarFecha1=true;
					//echo "1";
				}
			}
		}
		if($campo['VOLEXTFECHA1']<>'0001-01-01'){
			if($campo['VOLEXTFECHA1']<>'1900-01-01'){
				if(strtotime($campo['VOLEXTFECHA1']) >= $now){
					$f=explode('-',$campo['VOLEXTFECHA1']);
					$ano=$f[0];
					$mes=$f[1];
					$dia=$f[2];
					$cod_fecha2=$ano.$mes.$dia;
					$QuitarFecha2=false;
					if($cod_fecha1==$cod_fecha2){
						$cod_fecha2 ='';
					}
				}else{
					$QuitarFecha2=true;
					//echo "2";
				}
			}
		}
		if($campo['VOLEXTFECHA2']<>'0001-01-01'){
			if($campo['VOLEXTFECHA2']<>'1900-01-01'){
				if(strtotime($campo['VOLEXTFECHA2']) >= $now){
					$f=explode('-',$campo['VOLEXTFECHA2']);
					$ano=$f[0];
					$mes=$f[1];
					$dia=$f[2];
					$cod_fecha3=$ano.$mes.$dia;
					$QuitarFecha3=false;
					if($cod_fecha1==$cod_fecha3){
						$cod_fecha3 ='';
					}
					if($cod_fecha2==$cod_fecha3){
						$cod_fecha3 ='';
					}
				}else{
					$QuitarFecha3=true;
					//echo "3";
				}
			}
		}
		$cod_fecha4='';
		$QuitarFecha4=true;

		if($QuitarFecha1 && $QuitarFecha2 && $QuitarFecha3 && $QuitarFecha4){
			print "<script>alert('Las Fechas de pago estan vencidas, por este motivo no se puede generar este volante de pago, informar a dsrt@unisimonbolivar.edu.co');</script>";
			exit;
		}



		$cod_valor1=$campo["VOLEXTVALOR"];
		$cod_valor2=$campo['VOLEXTVALOREX1'];
		$cod_valor3=$campo['VOLEXTVALOREX2'];
		$cod_valor4=0;
		$cod_iac=trim($campo['CTACODIAC']);
		$cod_banco=$campo['BCOCODIGO'];
		$ListaBancos = $this->listabancos($cod_iac);





		if($cod_fecha1<>''){
			$pago['fecha']='Ordinaria                '.date('d/m/Y',strtotime($campo['VOLEXTFECHA']));
			$pago['valor']=number_format($campo["VOLEXTVALOR"],0,".",",");
			$pagos['pago1']=$pago;
		}
		if($cod_fecha2<>''){
			$pago['fecha']='Extra Ordinaria I     '.date('d/m/Y',strtotime($campo['VOLEXTFECHA1']));
			$pago['valor']=number_format($campo['VOLEXTVALOREX1'],0,".",",");
			$pagos['pago2']=$pago;
		}
		if($cod_fecha3<>''){
			$pago['fecha']='Extra Ordinaria II    '.date('d/m/Y',strtotime($campo['VOLEXTFECHA2']));
			$pago['valor']=number_format($campo['VOLEXTVALOREX2'],0,".",",");
			$pagos['pago3']=$pago;
		}



		//$rta = $this->volanteDetalle($nro_ref);

		$conceptos = array();




		if(count($conceptos)<1){
			$p = array();
			$p['concepto']=$campo['VOLEXTDOCUMENTO'].' - '.$campo['DOCNOMBRE'];
			$p['valor']='$ '.number_format($campo["VOLEXTVALOR"],0,".",",");
			$conceptos[]=$p;

			/*$p['concepto']='* '.$campo['VOLEXTDESCRIPCION'];
			$p['valor']='';
			$conceptos[]=$p;*/

			$rta = explode('|',$campo['VOLEXTDESCRIPCION'] );
			foreach ($rta as $key => $value) {
				$p['concepto']='* '.$value;
				$p['valor']='';
				$conceptos[]=$p;
			}
		}





		/*+++++++++*/







		$observacion1='1. Para pagar este Volante tener en cuenta el Total a Pagar.';
		$observacion2='2. Estudiante por favor verifique su estado academico';
		$observacion21='  para saber el Semestre a matricular.';
		$DatosBanco="BANCO: ".$banco.' CUENTA N° '.$cuenta;




		$ob = $this->volanteOb($campo['DOCCODIGO']);
		$ob = explode('|', @$ob[0]);







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
  $y=190; //observaciones

  	$y+=10; //aqui
  	$pdf->SetY($y);
  foreach ($ob as $key => $value) {
  	$pdf->MultiCell(260, 10, $value,0,'J');
  	$pdf->ln(0);
  	//$pdf->Text(30, $y, $value);

  }


  /*$pdf->Text(30, 21+$y, $observacion1);
  $pdf->Text(30, 32+$y, $observacion2);
  $pdf->Text(30, 43+$y, $observacion21);*/

  $y=131; //conceptos
  if(isset($conceptos) && !empty($conceptos)){
  	foreach($conceptos as $pago){
  		$pdf->SetY($y);
  		$pdf->MultiCell(415, 10, $pago['concepto'],0);
  		//$pdf->Text(30, $y,$pago['concepto']);
  		//$pdf->SetY($y-8);
  		//$pdf->Cell(500);
  		$pdf->SetY($y);
  		$pdf->Cell(430);
  		$pdf->Cell(110,10,$pago['valor'],0,1,'R');
  		$y+=10;
  		if ($tod_volante!='S') break;
  	}
  }

  $y=211; //pagos
  if(isset($pagos) && !empty($pagos)){
  	foreach($pagos as $pago){
  		if($pago['valor']>0){
  			$pdf->Text(310, $y, $pago['fecha']);
  			$pdf->Text(470, $y, '$ '.$pago['valor']);
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

  $y=444; //observaciones

  	$y+=10; //aqui
  	$pdf->SetY($y);
  foreach ($ob as $key => $value) {
  	$pdf->MultiCell(260, 10, $value,0,'J');
	$pdf->ln(0);
  	//$y+=10; //aqui
  	//$pdf->Text(30, $y, $value);

  }
  /*$pdf->Text(30, 11+$y, $observacion1);
  $pdf->Text(30, 22+$y, $observacion2);
  $pdf->Text(30, 33+$y, $observacion21);*/

  $y=390;  //conceptos

  if(isset($conceptos) && !empty($conceptos)){
  	foreach($conceptos as $pago){
  		$pdf->SetY($y);
  		$pdf->MultiCell(415, 10, $pago['concepto'],0);
  		//$pdf->Text(30, $y,$pago['concepto']);
  		//$pdf->SetY($y-8);
  		//$pdf->Cell(500);
  		$pdf->SetY($y);
  		$pdf->Cell(430);
  		$pdf->Cell(110,10,$pago['valor'],0,1,'R');
  		$y+=10;
  		if ($tod_volante!='S') break;
  	}
  }


  $y=470;  //pagos
  foreach($pagos as $pago){
  	if($pago['valor']>0){
  		$pdf->Text(310, $y, $pago['fecha']);
  		$pdf->Text(470, $y, '$ '.$pago['valor']);
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

  if($cod_valor1>0 && $cod_fecha1<>''){
  	$fe=substr($pagos['pago1']['fecha'],-10);
  	$pdf->Text(80, $y,$fe );
  	$pdf->Text(80, $y+16, '$ '.$pagos['pago1']['valor']);
  }
  if($tod_volante=='S')
  {
  	$y+=46;
  	if($cod_valor2>0 && $cod_fecha2<>''){
  		$fe=substr($pagos['pago2']['fecha'],-10);
  		$pdf->Text(80, $y,$fe );
  		$pdf->Text(80, $y+16, '$ '.$pagos['pago2']['valor']);
  	}
  	$y+=46;
  	if($cod_valor3>0 && $cod_fecha3<>''){
  		$fe=substr($pagos['pago3']['fecha'],-10);
  		$pdf->Text(80, $y,$fe );
  		$pdf->Text(80, $y+16, '$ '.$pagos['pago3']['valor']);
  	}
  	$y+=46;
  	if($cod_valor4>0 && $cod_fecha4<>''){
  		$fe=substr($pagos['pago4']['fecha'],-10);
  		$pdf->Text(80, $y,$fe );
  		$pdf->Text(80, $y+16, '$ '.$pagos['pago4']['valor']);
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
  $pdf->Image("http://academico.unisimoncucuta.edu.co/barra/barcodes.php?code=UCCEAN128&escapesequences=true&text=".$barcode1."&imageformat=4&dpi=72&rotation=0&modulewidth=0.5&unit=mm",$x,20+$y,283,38,"jpg");
}


if($cod_valor2>0 && $cod_fecha2<>'' && $tod_volante=='S'){
  $barcode2    = '415'.$cod_iac;  //Fijo
  $barcode2    .= '8020'.str_pad($nro_ref,12,'0',STR_PAD_LEFT);
  $barcode2    .= '\\F3900'.str_pad($cod_valor2, 8, '0', STR_PAD_LEFT);
  $barcode2    .= '\\F96'.$cod_fecha2;
  $pdf->Image("http://academico.unisimoncucuta.edu.co/barra/barcodes.php?code=UCCEAN128&escapesequences=true&text=$barcode2&imageformat=4&dpi=72&rotation=0&modulewidth=0.5&unit=mm",$x,65+$y,283,38,"jpg");
}
if($cod_valor3>0 && $cod_fecha3<>'' && $tod_volante=='S'){
  $barcode3    = '415'.$cod_iac;  //Fijo
  $barcode3    .= '8020'.str_pad($nro_ref,12,'0',STR_PAD_LEFT);
  $barcode3    .= '\\F3900'.str_pad($cod_valor3, 8, '0', STR_PAD_LEFT);
  $barcode3    .= '\\F96'.$cod_fecha3;
  $pdf->Image("http://academico.unisimoncucuta.edu.co/barra/barcodes.php?code=UCCEAN128&escapesequences=true&text=$barcode3&imageformat=4&dpi=72&rotation=0&modulewidth=0.5&unit=mm",$x,110+$y,283,38,"jpg");
}
if($cod_valor4>0 && $cod_fecha4<>'' && $tod_volante=='S'){
  $barcode4    = '415'.$cod_iac;  //Fijo
  $barcode4    .= '8020'.str_pad($nro_ref,12,'0',STR_PAD_LEFT);
  $barcode4    .= '\\F3900'.str_pad($cod_valor4, 8, '0', STR_PAD_LEFT);
  $barcode4    .= '\\F96'.$cod_fecha4;
  $pdf->Image("http://academico.unisimoncucuta.edu.co/barra/barcodes.php?code=UCCEAN128&escapesequences=true&text=$barcode4&imageformat=4&dpi=72&rotation=0&modulewidth=0.5&unit=mm",$x,155+$y,283,38,"jpg");
}

if($download){
	$this->deletePdf();
	$pdf->Output(ROOTs.'files'.DS.'Volante_'.$id.'.pdf');
	$this->output_file(ROOTs.'files'.DS.'Volante_'.$id.'.pdf','Volante_'.$id.'.pdf','application/pdf');
}else{
	$pdf->Output();
}




}

}

?>
