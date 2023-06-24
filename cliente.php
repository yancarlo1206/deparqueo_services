<?php 

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('xdebug.var_display_max_depth', '1000');
ini_set('xdebug.var_display_max_children', '1024');
ini_set('xdebug.var_display_max_data', '4096');
//$data = array("address" => "La Playa");
//$data = array("user" => "201522112757", 'password' => "unisimon");
$data = array("tipovehiculo" => "1");
//$data = array("usuario" => "00203", 'correo' => "Kendyardila23@hotmail.com");
//$data = array("asistencia" => "245815");
//$data = '{"asistencia":"245815","estudiantes":[{"codigo":"2017220016829","asistio":"1"},{"codigo":"2017220016600","asistio":"1"},{"codigo":"2017220016805","asistio":"1"},{"codigo":"2017120016426","asistio":"1"},{"codigo":"2017220216630","asistio":"1"},{"codigo":"2017220016912","asistio":"1"},{"codigo":"2017160916330","asistio":"1"},{"codigo":"2017220016980","asistio":"1"},{"codigo":"2017220116838","asistio":"1"},{"codigo":"2015120113074","asistio":"1"}]}';
//$data = json_decode($data);
//$data = array("direccion" => "CALLE 30 N° 4-30 PATIO CENTRO", 'emailpersonal' => "Kendyardila23@hotmail.com", 'telefono' => "5712471");
//$data = array("Authorization" => "");
//$data = array("ticket" => "0005074969");
//$ch = curl_init("http://localhost/code/clients/martha");

///ENTRADA A PARQUEADERO POR RFID///
/*$data = array("rfid" => "0005074969");
$ch = curl_init("http://localhost/deparqueo_services/entrada_rfid");*/
////////////////////////////////////

///ENTRADA A PARQUEADERO AUTOMOVIL///
//$data = array("tipovehiculo" => "1");
//$ch = curl_init("http://localhost/deparqueo_services/entrada");
////////////////////////////////////



$data = array("ticket" => "AUT240223002");
$ch = curl_init("http://localhost/deparqueo_services/ticket");


///TEST BATHROOM///
$data = array("rfid" => "3");
$ch = curl_init("http://localhost/deparqueo_services/entrada_bathroom_rfid");

//$data = array("ticket" => "AUT240223002", "valorPorPagar" => "1000");
//$ch = curl_init("http://localhost/deparqueo_services/ticket_pago");


//$data = array("ticket" => "AUT240223002");
//$ch = curl_init("http://localhost/deparqueo_services/salida");

 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
 curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($data));
 //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','X-Authorization: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJjb2RpZ28iOiIwMDIwMyIsInRpcG8iOiJkb2NlbnRlIn0.oOf_khS-4ZBzyGomdKd2_QswKCS-w2aJNir4CGV5-iM'));
 curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
 $response = curl_exec($ch);
 curl_close($ch);
 if(!$response) {
     return false;
 }else{
 	var_dump($response);
}

/*$ch = curl_init();
curl_setopt( $ch, CURLOPT_URL, $url );
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
curl_setopt( $ch, CURLOPT_VERBOSE, true );
curl_setopt( $ch, CURLOPT_HEADER, true );
curl_setopt( $ch, CURLOPT_TIMEOUT, $this->timeout_limit );
curl_setopt( $ch, CURLOPT_ENCODING, '' );
curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 ); 
curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $data ) );
curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
   'Content-Type: application/json'
) );*/


?>