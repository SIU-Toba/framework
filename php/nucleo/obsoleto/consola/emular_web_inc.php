<?
require_once("nucleo/browser/http.php");				//Genera Encabezados de HTTP
require_once("nucleo/browser/sesion.php");				//Control de sesiones HTTP 
require_once("nucleo/browser/usuario_http.php");		//Validador de usuarios
require_once("nucleo/browser/recurso.php");				//Encapsulamiento de la llamada a recursos
require_once("nucleo/browser/debug.php");				//DUMP de arrays, arboles y estructuras centrales
require_once("nucleo/browser/vinculador.php");			//Vinculos a otros ITEMS
require_once("nucleo/browser/hilo.php");				//Canal de comunicacion inter-ejecutable
require_once("nucleo/browser/interface/formateo.php"); 	//Funciones de formateo de columnas
require_once("nucleo/browser/interface/ei.php");		//Elementos de interface
require_once("lib/parseo.php");			       	//Funciones de parseo

?>