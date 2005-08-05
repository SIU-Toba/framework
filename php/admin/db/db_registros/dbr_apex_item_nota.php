<?
//Generacion: 5-08-2005 17:08:50
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_item_nota extends db_registros_s
//db_registros especifico de la tabla 'apex_item_nota'
{
	function __construct($fuente=null, $min_registros=0, $max_registros=0 )
	{
		$def['tabla']='apex_item_nota';
		$def['columna'][0]['nombre']='item_nota';
		$def['columna'][0]['pk']='1';
		$def['columna'][0]['secuencia']='apex_item_nota_seq';
		$def['columna'][1]['nombre']='nota_tipo';
		//$def['columna'][1]['no_nulo']='1';
		$def['columna'][2]['nombre']='item_id';
		$def['columna'][3]['nombre']='item_proyecto';
		//$def['columna'][3]['no_nulo']='1';
		$def['columna'][4]['nombre']='item';
		//$def['columna'][4]['no_nulo']='1';
		$def['columna'][5]['nombre']='usuario_origen';
		$def['columna'][6]['nombre']='usuario_destino';
		$def['columna'][7]['nombre']='titulo';
		$def['columna'][8]['nombre']='texto';
		$def['columna'][9]['nombre']='leido';
		$def['columna'][10]['nombre']='bl';
		$def['columna'][11]['nombre']='creacion';
		parent::__construct( $def, $fuente, $min_registros, $max_registros);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "item = '{$id['item']}'";
		$where[] = "item_proyecto = '{$id['proyecto']}'";
		$this->cargar_datos($where);
	}	
}
?>