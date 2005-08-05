<?
//Generacion: 5-08-2005 17:08:50
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_item_proto extends db_registros_s
//db_registros especifico de la tabla 'apex_item_proto'
{
	function __construct($fuente=null, $min_registros=0, $max_registros=0 )
	{
		$def['tabla']='apex_item_proto';
		$def['columna'][0]['nombre']='item_proyecto';
		$def['columna'][0]['pk']='1';
		$def['columna'][0]['no_nulo']='1';
		$def['columna'][1]['nombre']='item';
		$def['columna'][1]['pk']='1';
		$def['columna'][1]['no_nulo']='1';
		$def['columna'][2]['nombre']='descripcion';
		$def['columna'][3]['nombre']='logica';
		parent::__construct( $def, $fuente, $min_registros, $max_registros);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "item_proyecto = '{$id['item_proyecto']}'";
		$where[] = "item = '{$id['item']}'";
		$this->cargar_datos($where);
	}
}
?>