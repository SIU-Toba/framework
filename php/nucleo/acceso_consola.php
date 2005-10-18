<?
/*
	Este archivo seria un el PA de consola.
*/
//Item INICIAL
define("apex_pa_item_inicial","toba,/consola/menu");//Pagina inicial
//---- Registra la solicitud en la base
define("apex_pa_registrar_solicitud","db");// VALORES POSIBLES: nunca, siempre, db
//---- Guarda el benchmark de la generacion del item
define("apex_pa_registrar_cronometro","db");//VALORES POSIBLES: nunca, siempre, db
# Nivel de log a ARCHVO
define("apex_pa_log_archivo",1);
define("apex_pa_log_archivo_nivel",2);
# Nivel de log a la DB
define("apex_pa_log_db",0);
define("apex_pa_log_db_nivel",2);
# Nivel de log a la pantalla
define("apex_pa_log_pantalla",1);
define("apex_pa_log_pantalla_nivel",2);
//---------------------------------------------------------------------------
//-- VALOR DE $TOBA_DIR --
$dir_toba = $_ENV['toba_dir'];
$dir_toba = "$dir_toba/php";
$dir_toba = str_replace("\\", "/", $dir_toba);					//Cambia limitadores a formato unix

$separador = (substr(PHP_OS, 0, 3) == 'WIN') ? ";.;" : ":.:";
ini_set("include_path", ini_get("include_path"). $separador . $dir_toba);
//---------------------------------------------------------------------------

require_once("nucleo/toba.php");
toba::get_nucleo()->acceso_consola();

//---------------------------------------------------------------------------
?>