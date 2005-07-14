<?
//Generacion: 14-07-2005 17:04:53
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_objeto_mt_me_etapa extends db_registros_s
//db_registros especifico de la tabla 'apex_objeto_mt_me_etapa'
{
	function __construct($fuente=null, $min_registros=0, $max_registros=0 )
	{
		$def['tabla']='apex_objeto_mt_me_etapa';
		$def['columna'][0]['nombre']='objeto_mt_me_proyecto';
		$def['columna'][0]['pk']='1';
		$def['columna'][0]['no_nulo']='1';
		$def['columna'][1]['nombre']='objeto_mt_me';
		$def['columna'][1]['pk']='1';
		$def['columna'][1]['no_nulo']='1';
		$def['columna'][2]['nombre']='posicion';
		$def['columna'][2]['pk']='1';
		$def['columna'][2]['no_nulo']='1';
		$def['columna'][3]['nombre']='etiqueta';
		$def['columna'][4]['nombre']='descripcion';
		$def['columna'][5]['nombre']='tip';
		$def['columna'][6]['nombre']='imagen_recurso_origen';
		$def['columna'][7]['nombre']='imagen';
		$def['columna'][8]['nombre']='objetos';
		$def['columna'][9]['nombre']='objetos_adhoc';
		$def['columna'][10]['nombre']='pre_condicion';
		$def['columna'][11]['nombre']='post_condicion';
		$def['columna'][12]['nombre']='gen_interface_pre';
		$def['columna'][13]['nombre']='gen_interface_post';
		$def['columna'][14]['nombre']='ev_procesar';
		$def['columna'][15]['nombre']='ev_cancelar';
		parent::__construct( $def, $fuente, $min_registros, $max_registros);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "objeto_mt_me_proyecto = '{$id['objeto_mt_me_proyecto']}'";
		$where[] = "objeto_mt_me = '{$id['objeto_mt_me']}'";
		$where[] = "posicion = '{$id['posicion']}'";
		$this->cargar_datos($where);
	}
}
?>