<?
//Generacion: 14-07-2005 17:04:53
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_objeto_mt_me extends db_registros_s
//db_registros especifico de la tabla 'apex_objeto_mt_me'
{
	function __construct($fuente=null, $min_registros=0, $max_registros=0 )
	{
		$def['tabla']='apex_objeto_mt_me';
		$def['columna'][0]['nombre']='objeto_mt_me_proyecto';
		$def['columna'][0]['pk']='1';
		$def['columna'][0]['no_nulo']='1';
		$def['columna'][1]['nombre']='objeto_mt_me';
		$def['columna'][1]['pk']='1';
		$def['columna'][1]['no_nulo']='1';
		$def['columna'][2]['nombre']='ev_procesar_etiq';
		$def['columna'][3]['nombre']='ev_cancelar_etiq';
		$def['columna'][4]['nombre']='ancho';
		$def['columna'][5]['nombre']='alto';
		$def['columna'][6]['nombre']='posicion_botonera';
		$def['columna'][7]['nombre']='tipo_navegacion';
		$def['columna'][8]['nombre']='con_toc';
		$def['columna'][9]['nombre']='incremental';
		$def['columna'][10]['nombre']='debug_eventos';
		$def['columna'][11]['nombre']='activacion_procesar';
		$def['columna'][12]['nombre']='activacion_cancelar';
		$def['columna'][13]['nombre']='ev_procesar';
		$def['columna'][14]['nombre']='ev_cancelar';
		$def['columna'][15]['nombre']='objetos';
		$def['columna'][16]['nombre']='post_procesar';
		$def['columna'][17]['nombre']='metodo_despachador';
		$def['columna'][18]['nombre']='metodo_opciones';
		parent::__construct( $def, $fuente, $min_registros, $max_registros);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "objeto_mt_me_proyecto = '{$id['objeto_mt_me_proyecto']}'";
		$where[] = "objeto_mt_me = '{$id['objeto_mt_me']}'";
		$this->cargar_datos($where);
	}
}
?>