<?
//Generacion: 5-08-2005 17:08:50
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_item_zona extends db_registros_s
//db_registros especifico de la tabla 'apex_item_zona'
{
	function __construct($fuente=null, $min_registros=0, $max_registros=0 )
	{
		$def['tabla']='apex_item_zona';
		$def['columna'][0]['nombre']='proyecto';
		$def['columna'][0]['pk']='1';
		$def['columna'][0]['no_nulo']='1';
		$def['columna'][1]['nombre']='zona';
		$def['columna'][1]['pk']='1';
		$def['columna'][1]['no_nulo']='1';
		$def['columna'][2]['nombre']='nombre';
		$def['columna'][2]['no_nulo']='1';
		$def['columna'][3]['nombre']='clave_editable';
		$def['columna'][4]['nombre']='archivo';
		$def['columna'][4]['no_nulo']='1';
		$def['columna'][5]['nombre']='descripcion';
		parent::__construct( $def, $fuente, $min_registros, $max_registros);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "proyecto = '{$id['proyecto']}'";
		$where[] = "zona = '{$id['zona']}'";
		$this->cargar_datos($where);
	}
}
?>