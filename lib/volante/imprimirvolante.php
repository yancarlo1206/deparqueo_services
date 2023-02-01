<?php
//include('../../sys_seguridad.php');
include('volante.php');

$volante = new Volante();
$vol = $_GET['volante'];
$volante->imprimir($vol,true);
?>