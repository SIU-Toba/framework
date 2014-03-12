<?php

$dir = $_SERVER['TOBA_DIR']."/php";
$separador = (substr(PHP_OS, 0, 3) == 'WIN') ? ';.;' : ':.:';
ini_set('include_path', ini_get('include_path'). $separador . $dir);
require_once("nucleo/toba_nucleo.php");
toba_nucleo::instancia()->iniciar_contexto_desde_consola($_SERVER['TOBA_INSTANCIA'], $_SERVER['TOBA_PROYECTO'] );

//$dir_logs = toba_modelo_instalacion::dir_base()."/logs_test";
//toba_logger::instancia()->set_directorio_logs($dir_logs);
//toba_logger::instancia()->guardar_en_archivo('comandos.log');
//toba::logger()->guardar();
