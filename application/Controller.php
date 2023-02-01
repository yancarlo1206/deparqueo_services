<?php

abstract class Controller {

     public function __construct() {

    }

    abstract public function index();

    protected function loadModel($modelo) {
        
        $modelo = $modelo . 'Model';
        $rutaModelo = ROOT . 'models' . DS . $modelo . '.php';

        if (is_readable($rutaModelo)) {
            require_once $rutaModelo;
            $modelo = new $modelo;
            return $modelo;
        } else {            
            throw new Exception('Error de modelo ' . $modelo);
            //Session::set("error", "El modelo <b>" . $modelo . "</b> no fue encontrado");
            header("Location: ". BASE_URL."error/");
        }
    }

    protected function getLibrary($libreria) {
        $rutaLibreria = ROOT . 'lib' . DS . $libreria . '.php';

        if (is_readable($rutaLibreria)) {
            require_once $rutaLibreria;
        } else {
            throw new Exception('Error de libreria');
            //Session::set("error", "La librer&iacute;a <b>" . $libreria . "</b> no fue encontrada");
            header("Location: ". BASE_URL."error/");
        }
    }

    protected function validarToken1($codigo, $token){
        $this->_autenticacion = $this->loadModel('autenticacion');
        $temp = $this->_autenticacion->findByObject(array('codigo' => $codigo, 'token' => $token));
        return $temp;
    }
    
    /**
     * Método que permite eliminar los caracteres especiales de ciertos parámetros
     * como en los nombres de usuario y/o pasword para evitar ataques con inyección de código SQL
     * @param type $clave, el nombre que identifica el parámetro en POST
     * @return string, el parámetro libre de caracteres especiales o vacio en caso de que el parámetro sea vacio
     */
    protected function getTexto($clave) {
        if (isset($_POST[$clave]) && !empty($_POST[$clave])) {
            $_POST[$clave] = htmlspecialchars($_POST[$clave], ENT_QUOTES);
            return $_POST[$clave];
        }

        return '';
    }
    
    /**
     * Método que permite limpiar caracteres especiales y alfabeticos del valor que recibe como parámetro
     * @param type $clave, el parametro que se quiere pasar a caracteres numericos
     * @return int, el parametro con solo caracteres numericos, 0 en caso de no encontrar el parametro en POST
     */
    protected function getInt($clave) {
        if (isset($_POST[$clave]) && !empty($_POST[$clave])) {
            $_POST[$clave] = filter_input(INPUT_POST, $clave, FILTER_VALIDATE_INT);
            return $_POST[$clave];
        }

        return 0;
    }
    
    /**
     * Método que permite convertir un valor en un numero entero, si la conversión no es
     * posible retorna el valor de cero
     * @param type $int, el numero que se dese validar
     * @return int, el numero en caso de ser un valor valido, 0 en caso contrario
     */
    protected function filtrarInt($int) {
        $int = (int) $int;

        if (is_int($int)) {
            return $int;
        } else {
            return 0;
        }
    }
    
    /**
     * Método que permite obtener un valor POST de acuerdo al parametro recibido
     * @param type $clave, el parametro que se quiere consultar en POST
     * @return type, en valor almacenado en POST para el parametro especificado
     */
    protected static function getPostParam($clave) {
        if (isset($_POST[$clave])) {
            return $_POST[$clave];
        }else{
            return null;
        }
    }

    /**
     * Método que permite eliminar etiqeutas html y php de la cadena que recibe como parámetro y
     * limpiar los espacion en blanco
     * @param type $clave, el nombre que identifica el parámetro en POST
     * @return string, el parámetro libre de caracteres especiales o vacio en caso de que el parámetro sea vacio
     */    
    protected function getSql($clave) {
        
        if (isset($_POST[$clave]) && !empty($_POST[$clave])) {
            $_POST[$clave] = strip_tags($_POST[$clave]); // strip_tags Retira las etiquetas HTML y PHP de un string
            return trim($_POST[$clave]); // limpia los espacios en blanco al inicio y final de la cadena
        }
    }
    
    /**
     * Método que permite eliminar los carcteres especiales de la cadena que recibe como parámetro
     * las letras de la A a la Z y los numeros del 0 al 9 son los unicos que no seran eliminados
     * @param type $clave, la cadena que se quiere limpiar
     * @return type, la cadena sin caracteres especiales
     */
    protected function getAlphaNum($clave) {
        if (isset($_POST[$clave]) && !empty($_POST[$clave])) {
            $_POST[$clave] = (string) preg_replace('/[^A-Z0-9_]/i', '', $_POST[$clave]);
            return trim($_POST[$clave]);
        }
    }
    /**
     * Método que permite validar que el valor que se recibe como parámetro corresponde con la sintaxis
     * de una dirección de correo electrónico valida
     * @param type $email, la cadena de texto que se quiere validar
     * @return boolean, true en caso que el valor del parámetro sea valido, false en caso contrario
     */
    public function validarEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }
    
    public function getFecha($fecha)
    {
        if($fecha != ""){
            $split = explode("/", $fecha);
            return $fecha = $split[1] . "/" . $split[0] . "/" . $split[2];
        }
        else
            return null;
    }
    
    private function safe_b64decode($string) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    public function decodeApp($value){
        if(!$value){return false;}
        $crypttext = $this->safe_b64decode($value);
        $hashKey = "4f6a6d832be79";
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $hashKey, $crypttext, MCRYPT_MODE_ECB, $iv);
        return trim($decrypttext);
    }

    private  function safe_b64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }

    public function encodeApp($value){
        if(!$value){return false;}
        $text = $value;
        $hashKey = "4f6a6d832be79";
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $hashKey, $text, MCRYPT_MODE_ECB, $iv);
        return trim($this->safe_b64encode($crypttext)); 
    }

    public function enviarCorreo($destion='',$destionNombre='',$asunto='',$msj='') {
       
        $this->getLibrary("mail" . DS . "class.phpmailer");

        $mail = new PHPMailer();
        //indico a la clase que use SMTP
        $mail->IsSMTP();
        //permite modo debug para ver mensajes de las cosas que van ocurriendo
        $mail->SMTPDebug = false;
        //Debo de hacer autenticación SMTP
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "ssl";
        //indico el servidor de Gmail para SMTP
        $mail->Host = "SMTP.gmail.com";
        //indico el puerto que usa Gmail
        $mail->Port = 465;
        $mail->IsHTML(true);
        //indico un usuario / clave de un usuario de gmail
        $mail->Username = "no_responder@unisimonbolivar.edu.co";
        $mail->Password = "tzjhjmaiweaopytn";
        $mail->SetFrom('no_responder@unisimonbolivar.edu.co', 'Universidad Simon Bolivar - Cucuta');
        //$mail->AddReplyTo("migueleduardo23@gmail.com", "Miguel Ropero");
        $mail->Subject = $asunto." - UNISIMON CUCUTA";
        $mail->MsgHTML($this->templateEmail($msj));
        //indico destinatario
        //$address = $email;
        //$mail->AddAddress($address);
        $mail->AddAddress($destion,$destionNombre);

        if ($mail->Send()) {
            return true;
        } else {
            return false;
        }
    }

    public function enviarCorreo2($de='',$calve='',$nombre='',$asunto='',$msj='',$bcc='',$file=false) {
       
        $this->getLibrary("mail" . DS . "class.phpmailer");

        $mail = new PHPMailer();
        //indico a la clase que use SMTP
        $mail->IsSMTP();

        $mail->SMTPDebug  = true; 
        //permite modo debug para ver mensajes de las cosas que van ocurriendo
        $mail->SMTPDebug = false;
        //Debo de hacer autenticación SMTP
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "ssl";
        //indico el servidor de Gmail para SMTP
        $mail->Host = "SMTP.gmail.com";
        //indico el puerto que usa Gmail
        $mail->Port = 465;
        $mail->IsHTML(true);
        //indico un usuario / clave de un usuario de gmail
        $mail->Username = $de;
        $mail->Password = $calve;
        $mail->SetFrom($de, $nombre);
        
        $mail->Subject = $asunto." - UNISIMON CUCUTA";
        $mail->MsgHTML($this->templateEmail($msj));

        if ($file) {
            $varname = $file['name'];
            $vartemp = $file['tmp_name'];
            $mail->AddAttachment($vartemp, $varname);
        }

        if($bcc!=''){ foreach($bcc as $email){ $mail->AddBCC(trim($email)); } } 

        //indico destinatario
        //$address = $email;
        //$mail->AddAddress($address);
        $mail->AddAddress($de,$nombre);
      

        if ($mail->Send()) {
            return false;
        } else {
            return $mail->ErrorInfo;
        }
    }

    public function templateEmail($msj='')
    {
        $h = '<div style="background:#f5f5f5;font-family:Helvetica;padding:15px 0"><div style="width:600px;border:1px solid #fff;margin:0 auto"><div style="background:url(http://academico.unisimoncucuta.edu.co/views/layout/default/img/bg_header.png); padding: 14px">        <img src="http://academico.unisimoncucuta.edu.co/views/layout/default/img/logo.png" />    </div><div style="font-size:14px;padding: 14px; color: #333; background: #fff">';
        $f = '<hr style="background:none;border:0; border-top:1px solid #ddd; margin: 14px 0 0 0"> <p style="font-size:11px; color: #666; background: #f5f5f5; padding: 10px 5px; margin: 5px 0"> Copyright '.date('Y').' <a style="text-decoration: none; color:#468847" href="http://unisimoncucuta.edu.co/es/">unisimoncucuta.edu.co</a>, Todos los derecho reservados. <br> Universidad Sim&oacute;n Bol&iacute;var Extensi&oacute;n C&uacute;cuta <br> Sede Administrativa Av 3 # 13-34 La Playa Sede A PBX: 5712621. Fax: 5712735 Cucuta, Colombia '.date('Y').' </p> <hr style="background:none;border:0; border-top:1px solid #ddd; margin: 0 0 14px 0"> <table style="width: 100px; margin: 0 auto" border="0" cellspacing="0" cellpadding="0" style=""> <tbody> <tr></tr> <tr> <td><a href="https://www.facebook.com/UnisimonExt.Cucuta" target="_blank"><img src="http://unisimoncucuta.edu.co/images/icon_face.jpg" alt="" width="38" height="45" border="0"></a></td> <td><img src="http://unisimoncucuta.edu.co/images/spacer.gif" alt="" width="14" height="1"></td> <td><a href="http://twitter.com/unisimoncucut" target="_blank"><img src="http://unisimoncucuta.edu.co/images/icon_twitter.jpg" alt="" width="38" height="45" border="0"></a></td> <td><img src="http://unisimoncucuta.edu.co/images/spacer.gif" alt="" width="14" height="1"></td> <td><a href="http://www.youtube.com/user/UnisimonCucuta?feature=mhee" target="_blank"><img src="http://unisimoncucuta.edu.co/images/icon_you.jpg" alt="" width="38" height="45" border="0"></a></td> <td><img src="http://unisimoncucuta.edu.co/images/spacer.gif" alt="" width="14" height="1"></td> <td></td> </tr> </tbody> </table> </div> </div> </div>';
        return $h.$msj.$f;
    }

    public function numberPad($text,$n = 6,$s = "*") {
        return str_pad($text,$n,$s,STR_PAD_RIGHT);
    }

}

?>
