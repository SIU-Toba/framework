<?
include_once("nucleo/lib/elemento_toba.php");
/*
	$elemento = new elemento_toba_objeto();
	//$elemento->cargar_db("toba","638");
	$elemento->cargar_db("toba","777");
	$elemento->info();
*/
///*
	$elemento = new elemento_toba_item();
	//$elemento->cargar_db("toba","/admin/objetos/editores/mt_me");
	//$elemento->cargar_db("toba","/admin/items/propiedades");
	$elemento->cargar_db("toba","/pruebas/capas/1");
	
	ei_arbol( $elemento->exportar_sql_insert() ,"SQL");
	pre( $elemento->exportar_php() );
	$elemento->info();
//*/
?>