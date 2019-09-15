<?php

ini_set('xdebug.var_display_max_depth', '1000');
ini_set('xdebug.var_display_max_children', '1024');
ini_set('xdebug.var_display_max_data', '4096');

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', realpath(dirname(__FILE__)) . DS);

date_default_timezone_set('America/Bogota');

class Server {

  public function serve() {

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, X-Authorization");
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
    include('controllers/basicoController.php');
    $basico = new basicoController();
    echo $basico->entrada($data);  
  }

  private function entrada_rfid($data){
    include('controllers/basicoController.php');
    $basico = new basicoController();
    echo $basico->entrada_rfid($data);  
  }

  private function salida($data){
    include('controllers/basicoController.php');
    $basico = new basicoController();
    echo $basico->salida($data);  
  }


  private function handle_base($method) {
    switch($method) {
      case 'GET':
        $this->result();
        break;
      default:
        header('HTTP/1.1 405 Method Not Allowed');
        header('Allow: GET');
        break;
    }
  }

  private function handle_name($method, $name) {
    switch($method) {
      case 'POST':
        $this->create_contact($name);
        break;
      case 'PUT':
        $this->create_contact($name);
        break;
      case 'DELETE':
        $this->delete_contact($name);
        break;
      case 'GET':
        $this->display_contact($name);
        break;
      default:
        header('HTTP/1.1 405 Method Not Allowed');
        header('Allow: GET, PUT, DELETE');
        break;
      }
  }

  private function create_contact($name){
    if (isset($this->contacts[$name])) {
      header('HTTP/1.1 409 Conflict');
      return;
    }
    $data = json_decode(file_get_contents('php://input'));
    if (is_null($data)) {
      header('HTTP/1.1 400 Bad Request');
      $this->result();
      return;
    }
    $this->contacts[$name] = $data; 
    $this->result();
  }

  private function delete_contact($name) {
    if (isset($this->contacts[$name])) {
      unset($this->contacts[$name]);
      $this->result();
    } else {
      header('HTTP/1.1 404 Not Found');
    }
  }

  private function display_contact($name) {
    if (array_key_exists($name, $this->contacts)) {
      echo json_encode($this->contacts[$name]);
    } else {
      header('HTTP/1.1 404 Not Found');
    }
  }

  private function paths($url) {
    $uri = parse_url($url);
    return $uri['path'];
  }

}

$server = new Server;
$server->serve();

?>