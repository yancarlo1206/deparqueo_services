<?php
class NumeroDeVolante{
	
	private $NumVolante;
	private $TipoDocumento;
	private $NumVolConsultado;
	private $conexion;
	
	public function __construct($con,$TipoDoc=30){
		    $this->conexion=$con;
			$this->TipoDocumento=$TipoDoc;
			$this->NumVolConsultado=$this->ConsecutivoVolante();
			$this->NumVolConsultado=$this->NumVolConsultado+1;
			$this->Dig_Ver($this->NumVolConsultado);
	}
	private function ConsecutivoVolante(){
		//include('../includes/conexion_odbc.php');
		 //tipodoc=30 que es: otros volantes
		$sql="SELECT C.CONSVOLANTES FROM CONSECUTIVOS C WHERE C.TIPODOC=".$this->TipoDocumento;
		$result_fun= odbc_exec($this->conexion,$sql); print odbc_error();
		if(odbc_num_rows($result_fun)>0){
			$campo= odbc_fetch_array($result_fun);
			@odbc_free_result($result_fun);
			return $campo['CONSVOLANTES'];
		}else{
			print"No hay registro de consecutivos para volantes";
			exit;
		}
 	}
	private function suma($sarta){
	  $ss=0;
	  for ($I=0 ; $I <= strlen($sarta)-1 ; $I++) {
		$temp=substr($sarta, $I, 1);
		  $ss=$ss + $temp;
	  }		
	  return $ss;
  	}
	
	public function Dig_Ver($Numero){
	  $cambio=1;
	   $sarta="";
	  for ($I=strlen($Numero)-1 ; $I>=0 ; $I--) {
		  if($cambio==1){
			  $cad=substr($Numero, $I, 1)*2;				
			  $cambio=2;
		  }
		  else{
			  $cad=substr($Numero, $I, 1)*1;				
			  $cambio=1;
		  }
		  if($cad>=10){				
			$t1=substr($cad, 0, 1);
			if($t1==""){
				$t1=0;
			}
			$t2=substr($cad, 1, 1);
			if($t2==""){
				$t2=0;
			}	
			  $cad=$t1 + $t2;				
		  }
		$sarta=$sarta.$cad;			
	  }
	  $dig_ver1=$this->suma($sarta);        
	  $sarta="";
	  $cambio=1;
	  for ($I = strlen($Numero)-1 ; $I >=0 ; $I--) {
		  if($cambio==1){
			  $cad=substr($Numero, $I, 1)*1;
			  $cambio=2;
		  }
		  else{
			  $cad=substr($Numero, $I, 1)*3;
			  $cambio=1;
		  }
		  if($cad>10){
			$t1=substr($cad, 0, 1);
			if($t1==""){
				$t1=0;
			}
			$t2=substr($cad, 1, 1);
			if($t2==""){
				$t2=0;
			}	
			  $cad=$t1 + $t2;                
		  }
		$sarta=$sarta.$cad;
	  }		
	  $dig_ver2=$this->suma($sarta);		
	  $dig_ver=substr($dig_ver1, strlen($dig_ver1)-1, 1).substr($dig_ver2, strlen($dig_ver2)-1, 1);		
	  //return $dig_ver;
	  $this->NumVolante=$this->NumVolConsultado.$dig_ver;
  }
  
  public function GetNumeroVolante(){
			return $this->NumVolante;  
  }
  
  public function SetAsignarNuevoNumero(){
		$sql="UPDATE CONSECUTIVOS SET CONSVOLANTES=$this->NumVolConsultado WHERE TIPODOC=$this->TipoDocumento";
		odbc_exec($this->conexion,$sql);print odbc_error();
  }
	
	
}
//$Vol=new NumeroDeVolante(31);
//$NumVol=$Vol->GetNumeroVerificacion();
//$Vol->SetAsignarNuevoNumero();
?>