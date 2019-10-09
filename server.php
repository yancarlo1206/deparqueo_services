<?php

error_reporting(1);

ini_set('xdebug.var_display_max_depth', '1000');
ini_set('xdebug.var_display_max_children', '1024');
ini_set('xdebug.var_display_max_data', '4096');

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', realpath(dirname(__FILE__)) . DS);

date_default_timezone_set('America/Bogota');

class Server {

  public function serve() {

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept,Access-Control-Request-Method, X-Authorization");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    $uri = $_SERVER['REQUEST_URI'];
    $method = $_SERVER['REQUEST_METHOD'];
    if(isset($_SERVER['HTTP_X_AUTHORIZATION'])){
      $token = $_SERVER['HTTP_X_AUTHORIZATION'];
    }
    $paths = explode('/', $this->paths($uri));
    $paths = array_diff($paths, array('deparqueo_services'));
    array_shift($paths);
    $resource = array_shift($paths);
    $data = json_decode(file_get_contents('php://input'));
    if(!isset($_SERVER['HTTP_X_AUTHORIZATION'])){
      $this->$resource($data);
    }else{
      $this->$resource($token, $data);
    }

  }

  private function login($data){
    include('controllers/basicoController.php');
    $basico = new basicoController();
    echo $basico->login($data);
  }

  private function login_rfid($data){
    include('controllers/basicoController.php');
    $basico = new basicoController();
    echo $basico->login_rfid($data);
  }

  private function entrada($data){
    include('controllers/parqueoController.php');
    $basico = new parqueoController();
    echo $basico->entrada($data);  
  }

  private function entrada_rfid($data){
    include('controllers/parqueoController.php');
    $basico = new parqueoController();
    echo $basico->entrada_rfid($data);  
  }

  private function salida($data){
    include('controllers/parqueoController.php');
    $basico = new parqueoController();
    echo $basico->salida($data);  
  }

  private function ticket($data){
    include('controllers/ticketController.php');
    $basico = new ticketController();
    echo $basico->ticket($data);  
  }

  private function ticket_pago($data){
    include('controllers/ticketController.php');
    $basico = new ticketController();
    echo $basico->ticket_pago($data);  
  }

  private function ticket_pago_adicional($data){
    include('controllers/ticketController.php');
    $basico = new ticketController();
    echo $basico->ticket_pago_adicional($data);  
  }

  private function consulta_pago($data){
    include('controllers/ticketController.php');
    $basico = new ticketController();
    echo $basico->consulta_pago($data);  
  }

  private function paths($url) {
    $uri = parse_url($url);
    return $uri['path'];
  }

}

$server = new Server;
$server->serve();

?>