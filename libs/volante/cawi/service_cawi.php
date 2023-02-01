<?php

@define('DS', DIRECTORY_SEPARATOR);
@define('ROOTs', realpath(dirname(__FILE__)) . DS);


//$_POST['key'] = '';
$rta = array();
$rta['error'] = false;

if(!isset($_POST['key'])){
	$rta['error'] = "Error";
	echo json_encode($rta);
	exit;
}

$aut = new GoogleAu();
$aut->setKey('DQDWJMP6GUCBSTGQW4Y2CPN26NCERE5Y5Z5');

/*if(!$aut->verify($_POST['key'])){
	$rta['error'] = "Error de sincronia";
	echo json_encode($rta);
	exit;
}*/


try{
	include_once('..'.DS.'db.php');
	$conn = Db2::getInstance('WSIAACA');
}catch(Exception $e){
	$rta['error'] = "No se puede conectar con el servidor.";
	echo json_encode($rta);
    exit;        
}

if(!$conn){
	$rta['error'] = "No se puede conectar con el servidor";
	echo json_encode($rta);
    exit;
}


//$_POST['cod'] = '20111224805';
//$_POST['pass'] = 'unisimon';

if(isset($_POST['cod']) && isset($_POST['pass'])){
	
	$clave = $_POST['pass'];
	$codigo = $_POST['cod'];
	
	if(is_float((float)$codigo)){
	
		$codigoFloat = (float) $codigo;
	}else{
		$codigoFloat = 0;
		//echo("dddd");
	}
	$sql = "sELECT '' as facultad, pu.usu_correo as correo, pu.usu_codigo as codigo, e.estnombre1 || ' ' || coalesce(e.estnombre2,'') || ' ' || e.estapellido1 || ' ' || coalesce(e.estapellido2,'') as nombre, es.estsexo as sexo, pr.prgcodprog as carrera, pr.prgnombre as nombrecarrera FROM db2admin.per_usuarios pu inner join db2admin.estudiantes e on pu.usu_codigo = e.estcodigo inner join db2admin.datosestuds es on pu.usu_codigo = es.estcodigo inner join db2admin.matriculas m on pu.usu_codigo = m.mtrcodigoest inner join relacion.vperiodoacademico p on  p.ano=m.mtrperiodoano and p.sem=m.mtrperiodosem inner join db2admin.programas pr on pr.prgcodprog = m.mtrprograma where (pu.usu_codigo = ".$codigoFloat." or  pu.usu_usuario='".$codigo."' ) and pu.usu_password = '".md5($clave)."'";//md5($clave)

	$est = ejecutar($conn,$sql);

	if(count($est)>0){
		$row = $est[0];
		$rta['codigo'] = $row['CODIGO'];
		$rta['sexo'] = ($row['SEXO']==1?'M':'F');
		$rta['facultad'] = $row['FACULTAD'];
		$rta['nombre'] = ucwords(strtolower(utf8_encode($row['NOMBRE'])));
		$rta['rol'] = "estudiante";
		$rta['correo'] = $row['CORREO'];
		$rta['estado'] = true;
		$rta['materias'] = array();
		$rta['nombrecarrera'] = $row['NOMBRECARRERA'];

		$rta['carrera'] = array();
		foreach ($est as $key => $e) {
			$rta['carrera'][$e['CARRERA']] =  $e['CARRERA'];
		}

		$sql = "sELECT a.asgcod as codigo, n.ntacodprog as CARRERA, n.ntagrpcod as grupo, a.asgnombre as nombre, v.descripcion as jornada FROM WEBSIA.VNOTAS n inner join relacion.vperiodoacademico p on  p.ano=n.ntaperiodoano and p.sem=n.ntaperiodosem inner join DB2ADMIN.ASIGNATURAS a on a.asgcod=n.nteasgcodeq inner join RELACION.JORNADA v on v.id=n.ntejornadaeq where n.ntacodestudi=".$rta['codigo']."";

		$mats = ejecutar($conn,$sql);
		foreach ($mats as $key => $mat) {
			$codigo = $mat['CARRERA'].'-'.$mat['CODIGO'].'-'.$mat['GRUPO'].'-'.$mat['JORNADA'][0];
			$rta['materias'][$codigo] = array(
				'codigo' => $codigo,
				'nombre' => ucwords(strtolower(utf8_encode($mat['NOMBRE']))) 
				);
		}

		

		if(count($rta['materias'])<1){
			$rta['estado'] = false;
		}

		//var_dump($rta);

	}else{
		//$codigo = str_pad($codigo,5, "0", STR_PAD_LEFT);
		$clave2 = substr($clave, 0, 12);


		$sql = "select '' as facultad, prgnombre as departamento, p.cedula, p.nombre1||' '||coalesce(p.nombre2,'')||' '||p.apellido1||' '||coalesce(p.apellido2,'') as NOMBRE, 
		p.sexo, p.correoinst as correo, d.coddocente as codigo from docente.docente d inner join docente.persona p on p.cedula=d.cedula 
		left outer join docente.contratoactual ca on ca.coddocente = d.coddocente
		left outer join docente.programa pr on pr.prgcodprog = ca.programa
		where d.coddocente='".$codigo."' and d.password='".getHash($clave2)."' and d.estado='A'";
		
		$pro = ejecutar($conn,$sql);
		//var_dump($pro);
		if(count($pro)>0){

			$row = $pro[0];

			$rta['codigo'] = $row['CODIGO'];
			$rta['sexo'] = ($row['SEXO']==1?'M':'F');

			if(isset($row['DEPARTAMENTO'])){
				$rta['departamento'] = $row['DEPARTAMENTO'];
			}

			$rta['nombre'] = ucwords(strtolower(utf8_encode($row['NOMBRE'])));

			$rta['facultad'] = $row['FACULTAD'];
			$rta['rol'] = "profesor";
			
			if(isset($row['CORREO'])){
				$rta['correo'] = $row['CORREO'];
			}else if(isset($row['CORREO2'])){
				$rta['correo'] = $row['CORREO2'];
			}else{
				$rta['correo'] = '-';
			}

		

			$rta['carrera'] = array();
			$rta['estado'] = true;
			$rta['materias'] = array();

			$sql = "select distinct h.hor_asignatura as codigo, h.hor_programa as carrera,h.hor_grupo as grupo, a.asgnombre as nombre,v.descripcion as jornada from DB2ADMIN.AS_HORARIOS h inner join docente.docente d on d.cedula = h.hor_docente inner join docente.contratoactual c on c.coddocente = d.coddocente inner join relacion.vperiodoacademico p on p.ano=h.hor_ano and p.sem=h.hor_sem inner join DB2ADMIN.ASIGNATURAS a on a.asgcod=h.hor_asignatura inner join RELACION.JORNADA v on v.id=h.hor_jornada where d.coddocente='".$rta['codigo']."'";

			$mats = ejecutar($conn,$sql);
			foreach ($mats as $key => $mat) {
			$codigo = $mat['CARRERA'].'-'.$mat['CODIGO'].'-'.$mat['GRUPO'].'-'.$mat['JORNADA'][0];

				$rta['materias'][$codigo] = array(
					'codigo' => $codigo,
					'nombre' => utf8_encode(ucwords(strtolower($mat['NOMBRE'])))
					);
				$rta['carrera'][$mat['CARRERA']]= $mat['CARRERA'];
			}

			$sql = "select * from relacion.reldocente r where r.coddocente='".$rta['codigo']."' ";
			$tem = ejecutar($conn,$sql);

			if(count($tem)<1){
				$rta['carrera'][1]= 1;
				$rta['materias']['0'] = array(
					'codigo' => '0',
					'nombre' => 'Administrativo - Sin materias asignadas')
					;
			}

			
			if(count($rta['materias'])<1){
				$rta['estado'] = false;
			}

			//var_dump($rta);

		}
	}
}else if( isset($_POST['carrera']) && $_POST['carrera']=='0' ){

	$sql = "select p.prgcodprog as codigo, p.prgnombre as nombre from docente.programa p where p.prgtipo=2 or p.prgtipo=1";
	$rta = ejecutar($conn,$sql);
	
	//oci_free_statement($query);
	//var_dump($rta);

}else if( isset($_POST['materia']) && $_POST['materia']=='0' ){

	$sql = "select distinct h.hor_asignatura as codigo, h.hor_programa as CARRERA,h.hor_grupo as grupo, a.asgnombre as nombre,v.descripcion as jornada, h.hor_semestre as semestre from DB2ADMIN.AS_HORARIOS h inner join relacion.vperiodoacademico p on p.ano=h.hor_ano and p.sem=h.hor_sem inner join DB2ADMIN.ASIGNATURAS a on a.asgcod=h.hor_asignatura inner join RELACION.JORNADA v on v.id=h.hor_jornada inner join docente.programa p on p.prgcodprog = h.hor_programa where p.prgtipo=2 or p.prgtipo=1";
	$mat = ejecutar($conn,$sql);
	
$rta=array();
	foreach ($mat as $key => $m) {
		$codigo = $m['CARRERA'].'-'.$m['CODIGO'].'-'.$m['GRUPO'].'-'.$m['JORNADA'][0];
		$rta[$codigo] = array(
					'semestre'=>(int)@$m['SEMESTRE'],
					'carrera'=>$m['CARRERA'],
					'codigo'=>$codigo,
					'nombre'=>utf8_encode($m['NOMBRE'])
					);
		
	}
	//oci_free_statement($query);
	//var_dump($rta);

}

echo preg_replace("/\r\n+|\r+|\n+|\t+/i", " ",json_encode($rta));

//oci_close($conn);
exit;
?>

<?php
function ejecutar($conn,$sql='')
{	
	return  $conn->getResultArray($conn->ejecutar($sql));
}
?>


<?php
class GoogleAu {
	protected $keyRegeneration = 30;
    protected $otpLength = 6;
    protected $key = '';
    protected $secret = '';

    public function __construct() {
       $this->generate();
    }

    /**
	 * @return string
	 **/
    public function getKey() {
    	return $this->key;
    }

    /** 
	 * Defines the key and generates the secret code.
	 *
	 * @param string $key.
	 **/
    public function setKey($value) {
    	$this->key = $value;
    	$this->secret = $this->getDecodeBase32($value);
    }

	/**
	 * Generates a 35 digit secret key in base32 format
	 * @return string
	 **/
	public function generate($length = 35) {
		$b32 	= "234567QWERTYUIOPASDFGHJKLZXCVBNM";
		$s 	= "";

		for ($i = 0; $i < $length; $i++)
			$s .= $b32[rand(0,31)];

		$this->setKey($s);
		return $s;
	}

	/**
	 * Returns the current Unix Timestamp devided by the keyRegeneration
	 * period.
	 * @return integer
	 **/
	private function getTimestamp() {
		return floor(microtime(true)/$this->keyRegeneration);
	}
  	
  	/**
	 * Decodes a base32 string into a binary string.
	 **/
	private function getDecodeBase32($b32) {

		$b32 	= strtoupper($b32);

		if (!preg_match('/^[ABCDEFGHIJKLMNOPQRSTUVWXYZ234567]+$/', $b32, $match))
			throw new Exception('Invalid characters in the base32 string.');

		$l 	= strlen($b32);
		$n	= 0;
		$j	= 0;
		$binary = "";
		$lut = array('A' => 0,'B' => 1,'C' => 2,'D' => 3,'E' => 4,'F' => 5,'G' => 6,'H' => 7,'I' => 8,'J' => 9,'K' => 10,'L' => 11,'M' => 12,'N' => 13,'O' => 14,'P' => 15,'Q' => 16,'R' => 17,'S' => 18,'T' => 19,'U' => 20,'V' => 21,'W' => 22,'X' => 23,'Y' => 24,'Z' => 25,'2' => 26,'3' => 27,'4' => 28,'5' => 29,'6' => 30,'7' => 31);
		for ($i = 0; $i < $l; $i++) {

			$n = $n << 5; 				// Move buffer left by 5 to make room
			$n = $n + $lut[$b32[$i]]; 	// Add value into buffer
			$j = $j + 5;				// Keep track of number of bits in buffer

			if ($j >= 8) {
				$j = $j - 8;
				$binary .= chr(($n & (0xFF << $j)) >> $j);
			}
		}

		return $binary;
	}

	/**
	 * Takes the secret key and the timestamp and returns the one time
	 * password.
	 *
	 * @param binary $key - Secret key in binary form.
	 * @param integer $counter - Timestamp as returned by getTimestamp.
	 * @return string
	 **/
	public function getNow()
	{	
		$key = $this->secret;
		$counter = $this->getTimestamp();
	    if (strlen($key) < 8)
		throw new Exception('Secret key is too short. Must be at least 16 base 32 characters');

	    $bin_counter = pack('N*', 0) . pack('N*', $counter);		// Counter must be 64-bit int
	    $hash 	 = hash_hmac ('sha1', $bin_counter, $key, true);

	    return str_pad($this->oath_truncate($hash), $this->otpLength, '0', STR_PAD_LEFT);
	}

	/**
	 * Verifys a user inputted key against the current timestamp. Checks $window
	 * keys either side of the timestamp.
	 *
	 * @param string $key - User specified key
	 * @param integer $window
	 * @param boolean $useTimeStamp
	 * @return boolean
	 **/
	public function verify($key, $window = 4, $useTimeStamp = true) {
		$b32seed = $this->key;
		$timeStamp = $this->getTimestamp();

		if ($useTimeStamp !== true) $timeStamp = (int)$useTimeStamp;

		$binarySeed = $this->getDecodeBase32($b32seed);

		for ($ts = $timeStamp - $window; $ts <= $timeStamp + $window; $ts++)
			if ($this->getNow() == $key)
				return true;

		return false;

	}

	/**
	 * Extracts the OTP from the SHA1 hash.
	 * @param binary $hash
	 * @return integer
	 **/
	private function oath_truncate($hash)
	{
	    $offset = ord($hash[19]) & 0xf;

	    return (
	        ((ord($hash[$offset+0]) & 0x7f) << 24 ) |
	        ((ord($hash[$offset+1]) & 0xff) << 16 ) |
	        ((ord($hash[$offset+2]) & 0xff) << 8 ) |
	        (ord($hash[$offset+3]) & 0xff)
	    ) % pow(10, $this->otpLength);
	}

	/**
	 * Generated url code QR.
	 * @param strings  $label
	 * @param strings  $label2
	 * @param integer  $size
	 * @return image/jpg in base64
	 **/
	public function getQr($label='',$label2='',$size=200) {
		return 'data:image/jpg;base64,'.
		base64_encode(
			file_POST_contents(
				"https://www.google.com/chart?chs=".
				$size."x".$size
				."&chld=M|0&cht=qr&chl=otpauth://totp/"
				.$label.":".$label2.
				"?secret=".$this->key.
				"&issuer=".$label.""
				)
			);
	}

	/**
	 * Time remaining.
	 * @return integer in seconds 
	 **/
	public function getTime() {
		$tem = $this->getTimestamp();
		$tem2= $tem;
		$s = 0;
		while ( $tem == $tem2) {
			$tem2= floor((microtime(true)+$s)/$this->keyRegeneration);
			$s+=1;
		}
		return $s-1;
	}

}

function getHash($data,$algoritmo='sha1', $key='4f6a6d832be79') {
        $hash = hash_init($algoritmo, HASH_HMAC, $key);
        hash_update($hash, $data);

        return hash_final($hash);
    }

?>