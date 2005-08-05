<?
//Generacion: 5-08-2005 17:08:50
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_item_msg extends db_registros_s
//db_registros especifico de la tabla 'apex_item_msg'
{
	function __construct($fuente=null, $min_registros=0, $max_registros=0 )
	{
		$def['tabla']='apex_item_msg';
		$def['columna'][0]['nombre']='item_msg';
		$def['columna'][0]['pk']='1';
		$def['columna'][0]['secuencia']='apex_item_msg_seq';
		$def['columna'][1]['nombre']='msg_tipo';
		$def['columna'][1]['no_nulo']='1';
		$def['columna'][2]['nombre']='indice';
		$def['columna'][2]['no_nulo']='1';
		$def['columna'][3]['nombre']='item_id';
		$def['columna'][4]['nombre']='item_proyecto';
		$def['columna'][4]['pk']='1';
		$def['columna'][4]['no_nulo']='1';
		$def['columna'][5]['nombre']='item';
		$def['columna'][5]['no_nulo']='1';
		$def['columna'][6]['nombre']='descripcion_corta';
		$def['columna'][7]['nombre']='mensaje_a';
		$def['columna'][8]['nombre']='mensaje_b';
		$def['columna'][9]['nombre']='mensaje_c';
		$def['columna'][10]['nombre']='mensaje_customizable';
		$def['columna'][11]['nombre']='parametro_patron';
		parent::__construct( $def, $fuente, $min_registros, $max_registros);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "item_msg = '{$id['item_msg']}'";
		$where[] = "item_proyecto = '{$id['item_proyecto']}'";
		$this->cargar_datos($where);
	}
}
?>